<?php
/**
 * Novalnet payment plugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Novalnet End User License Agreement
 *
 * DISCLAIMER
 *
 * If you wish to customize Novalnet payment extension for your needs,
 * please contact technic@novalnet.de for more information.
 *
 * @category    Novalnet
 * @package     NovalnetPayment
 * @copyright   Copyright (c) Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
declare(strict_types=1);

namespace Novalnet\NovalnetPayment\Helper;

use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Context;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Symfony\Component\Mime\Exception\RfcComplianceException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class NovalnetValidator
{

    /**
     * @var NovalnetHelper
     */
    private $helper;

    public function __construct(
        NovalnetHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Validates the given input data is numeric or not.
     *
     * @param int $input The input value.
     *
     * @return boolean
     */
    public function isValidDigit($input)
    {
        return (bool) (! empty($input) && preg_match('/^[0-9]+$/', "$input"));
    }

    /**
     * Check for success status
     *
     * @param array $data
     *
     * @return bool
     */
    public function isSuccessStatus(array $data): bool
    {
        return (bool) ((! empty($data['result']['status']) && 'SUCCESS' === $data['result']['status']) || (! empty($data['status']) && 'SUCCESS' === $data['status']));
    }

    /**
     * Checks for the given string in given text.
     *
     * @param string $string The string value.
     * @param string $data   The data value.
     *
     * @return boolean
     */
    public static function checkString($string, $data = 'novalnet')
    {
        if (!empty($string)) {
            return (false !== strpos($string, $data));
        }
        
        return false;
    }

    /**
     * Generate Checksum Token
     *
     * @param Request $request
     * @param string $accessKey
     * @param string $txnSecret
     *
     * @return bool
     */
    public function isValidChecksum(Request $request, string $accessKey, string $txnSecret): bool
    {
        $valid = false;
        
        if (! empty($request->get('checksum')) && ! empty($request->get('tid')) && ! empty($request->get('status')) && ! empty($accessKey) && ! empty($txnSecret)) {
            $checksum = hash('sha256', $request->get('tid') . $txnSecret . $request->get('status') . strrev($accessKey));
            if ($checksum === $request->get('checksum')) {
                return true;
            }
        }
        return $valid;
    }

    /**
     * Check for the authorize transaction
     *
     * @param array $paymentSettings
     * @param string $paymentCode
     * @param array $parameters
     *
     * @return bool
     */
    public function isAuthorize(array $paymentSettings, string $paymentCode, array $parameters): bool
    {
        $paymentCode = $this->helper->formatString($paymentCode);
        $manualCheckLimit = !empty($paymentSettings["NovalnetPayment.settings.$paymentCode.onHoldAmount"]) ? $paymentSettings["NovalnetPayment.settings.$paymentCode.onHoldAmount"] : 0;
        return (bool) (!empty($paymentSettings["NovalnetPayment.settings.$paymentCode.onHold"]) && 'authroize' === $paymentSettings["NovalnetPayment.settings.$paymentCode.onHold"] && (int) $parameters['transaction']['amount'] > 0 && (int) $parameters['transaction']['amount'] >= (int) $manualCheckLimit);
    }

    /**
     * Check the guarantee condition and return value.
     *
     * @param SalesChannelContext $salesChannelContext
     * @param mixed $transaction
     * @param array $settings
     * @param string $paymentMethod
     *
     * @return string
     */
    public function isGuaranteeAvailable(SalesChannelContext $salesChannelContext, $transaction, array $settings, string $paymentMethod): string
    {
        $paymentShortName = $this->helper->formatString($paymentMethod);
        
        if (!$this->helper->getSupports('guarantee', $paymentMethod) && !$this->helper->getSupports('instalment', $paymentMethod)) {
            return 'NO';
        }

		
        if ($salesChannelContext->getCurrency()->getIsoCode() !== 'EUR') {
            return 'NO';
        }
        
        $lineItem = [];
        if (method_exists($transaction, 'getOrder')) {
            $lineItem = $transaction->getOrder()->getLineItems()->getelements();
  
        } elseif (method_exists($transaction, 'getCart')) {
            $lineItem = $transaction->getCart()->getLineItems()->getelements();
        }
        
        foreach ($lineItem as $item => $price) {
            $states = $price->getStates()[0];
            if ($states == 'is-download') {
                return 'NO';
            }
        }
        
        $billingCustomer  = $billingAddress = $shippingCustomer = $shippingAddress = [];
        if (!is_null($salesChannelContext->getCustomer())) {
            list($billingCustomer, $billingAddress) = $this->helper->getAddress($salesChannelContext->getCustomer(), 'billling');
            list($shippingCustomer, $shippingAddress) = $this->helper->getAddress($salesChannelContext->getCustomer(), 'shipping');
        }

        if (! empty($shippingAddress) && $billingAddress !== $shippingAddress) {
            return 'NO';
        }

        if (! empty($shippingCustomer['company']) && !empty($billingAddress['company']) && $billingAddress['company'] !== $shippingCustomer['company']) {
            return 'NO';
        }
        
        if (!empty($billingAddress['company']) && !empty($settings["NovalnetPayment.settings.$paymentShortName.allowB2B"])) {
            $countriesList  = ['AT','DE','CH', 'BE', 'DK', 'BG', 'IT', 'ES', 'SE', 'PT', 'NL', 'IE', 'HU', 'GR', 'FR', 'FI', 'CZ'];
        } else {
            $countriesList  = ['AT','DE','CH'];
        }
        
        if (!in_array($billingAddress['country_code'], $countriesList)) {
            return 'NO';
        }
        $subproduct = $product = $discount = [];
        $orderAmount = 0;
        if (method_exists($transaction, 'getOrder')) {
            $subscriptionorder = $transaction->getOrder()->getExtensions();
            if (!empty($subscriptionorder['novalnetSubscription']) || !empty($subscriptionorder['subsOrders'])) {
                return 'NO';
            }
            $orderAmount = $this->helper->amountInLowerCurrencyUnit($transaction->getOrder()->getPrice()->getTotalPrice());
        } elseif (method_exists($transaction, 'getCart')) {
            $lineitem = $transaction->getCart()->getLineItems()->getelements();
            foreach ($lineitem as $item => $price) {
                if (isset($price->getextensions()['novalnetConfiguration'])) {
                    $id = $price->getExtensions()['novalnetConfiguration']['productId'];
                    if (isset($price->getExtensions()['novalnetConfiguration']['productId'])) {
                        $subproduct[$item] = [
                            'totalPrice' => $this->helper->amountInLowerCurrencyUnit($price->getprice()->gettotalPrice()),
                            'type' => $price->getType(),
                            'productId' =>$price->getExtensions()['novalnetConfiguration']['productId'],
                            'signUpFee' => $price->getExtensions()['novalnetConfiguration']['signUpFee'],
                            'freeTrial' => $price->getExtensions()['novalnetConfiguration']['freeTrial'],
                        ];
                        if ($price->getExtensions()['novalnetConfiguration']['discount'] !=null) {
                            $subproduct[$item]['discount'] = round((($price->getPrice()->getTotalPrice() / 100) * $price->getExtensions()['novalnetConfiguration']['discount']) * 100);
                        } else {
                            $subproduct[$item]['discount'] = 0;
                        }
                    }
                } else {
                    $product[$item] = [
                        'totalPrice' => $this->helper->amountInLowerCurrencyUnit($price->getprice()->gettotalPrice()),
                        'type' => $price->getType(),
                    ];
                }
            }
            $orderAmount = $this->helper->amountInLowerCurrencyUnit($transaction->getCart()->getPrice()->getTotalPrice());
        }

        if (empty($settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"])) {
            $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] = 999;
        }
        
        if (!empty($subproduct)) {
            $totalamount = $discountamount = $signupamount = 0;
            
            foreach ($subproduct as $item => $price) {
                if ($price['type'] == 'product') {
                    if ($price['freeTrial'] != 0) {
                        return 'NO';
                    }
                    
                    $productid = $price['productId'];
                    $discountamount = $price['discount'];
                    $signupamount = $price['signUpFee'];
                    $totalamount = $price['totalPrice'];
                }
                
                $productAmount = $totalamount - $discountamount;
                if (0 < (int) $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] && (int) $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] > (int) $productAmount) {
                    return 'NO';
                }
            }
        }
        
        if (!empty($product)) {
            foreach ($product as $item => $price) {
                if ($price['type'] == 'product') {
                    $totalamount = $price['totalPrice'];
                    if (0 < (int) $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] && (int) $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] > (int) $totalamount) {
                        return 'NO';
                    }
                }
            }
        }
        
        if (!empty($orderAmount)) {
            if (0 < (int) $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] && (int) $settings["NovalnetPayment.settings.$paymentShortName.minimumOrderAmount"] > (int) $orderAmount) {
                return 'NO';
            }
            
            if (in_array($paymentMethod, ['novalnetinvoiceinstalment', 'novalnetsepainstalment'])) {
                $count= 0;
                foreach ($settings["NovalnetPayment.settings.$paymentShortName.cycles"] as $values) {
                    if (($orderAmount / $values) >= 999) {
                        $count++;
                    }
                }

                if ($count == 0  || empty($settings["NovalnetPayment.settings.$paymentShortName.cycles"])) {
                    return 'NO';
                }
            }
        }

        if (!empty($settings["NovalnetPayment.settings.$paymentShortName.allowB2B"])) {
            if (!empty($billingAddress['company'])) {
                return 'HIDE_DOB';
            }
        }

        return 'YES';
    }

    /**
     * Check mail if validate or not.
     *
     * @param string $mail
     *
     * @return bool
     */
    public function isValidEmail($mail): bool
    {
        return (bool) (new EmailValidator())->isValid($mail, new RFCValidation());
    }
}
