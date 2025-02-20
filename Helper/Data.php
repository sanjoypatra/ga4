<?php

namespace Meetanshi\GA4\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Data
 * @package Meetanshi\GA4\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const MODULE_ENABLE = 'mt_ga4/general/enable';
    /**
     *
     */
    const GA_JS_CODE = 'mt_ga4/general/gt_jscode';
    /**
     *
     */
    const GA_NONJS_CODE = 'mt_ga4/general/gt_nonjscode';
    /**
     *
     */
    const IMPRESSION_CHUNK_SIZE = 'mt_ga4/general/impressionc_size';
    /**
     *
     */
    const PRODUCT_IDENTIFIER = 'mt_ga4/general/product_identifier';
    /**
     *
     */
    const PRODUCT_BRAND = 'mt_ga4/general/product_brand';
    /**
     *
     */
    const PRODUCT_BRAND_ATTRIBUTE = 'mt_ga4/general/product_brand_attribute';
    /**
     *
     */
    const ENABLE_VARIANT = 'mt_ga4/general/enable_variant';
    /**
     *
     */
    const ORDER_TOTAL_CALCULATION = 'mt_ga4/general/order_total';
    /**
     *
     */
    const EXCLUDE_TAX_TRANSACTION = 'mt_ga4/general/exclude_order_trans';
    /**
     *
     */
    const EXCLUDE_SHIP_TRANSACTION = 'mt_ga4/general/exclude_shipping_trans';
    /**
     *
     */
    const EXCLUDE_SHIP_INCLUDING_TAX = 'mt_ga4/general/exclude_shipping_includetax';
    /**
     *
     */
    const SUCCESS_PATH = 'mt_ga4/general/success_path';
    /**
     *
     */
    const EXCLUDE_ZERO_ORDER = 'mt_ga4/general/exclude_order_zero';
    /**
     *
     */
    const MEASURE_PRODUCT_CLICK = 'mt_ga4/general/measure_product_click';
    /**
     *
     */
    const CHILD_PARENT = 'mt_ga4/general/child_parent';
    /**
     *
     */
    const GTM_ACCOUNT_ID = 'mt_ga4/gtm_api/account_id';
    /**
     *
     */
    const GTM_CONTAINER_ID = 'mt_ga4/gtm_api/container_id';
    /**
     *
     */
    const GTM_MEASUREMENT_ID = 'mt_ga4/gtm_api/measurement_id';
    /**
     *
     */
    const JSON_EXPORT_PUBLIC_ID = 'mt_ga4/json_export/public_id';
    /**
     *
     */
    const IS_MOVE_JS_BOTTOM = 'dev/js/move_script_to_bottom';
    /**
     *
     */
    const GTM_API_SECRET = 'mt_ga4/gtm_api/api_secret';
    /**
     *
     */
    const GTM_API_CURL = 'mt_ga4/gtm_api/is_curl';

    const GTM_API_CONVERSION_ID = 'mt_ga4/gtm_api/ads_conversion_id';

    const GTM_API_CONVERSION_LABEL = 'mt_ga4/gtm_api/ads_conversion_label';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var AttributeCollection
     */
    protected $productAttributes;
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $block;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    protected $quoteItemCollectionFactory;
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSession;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * @var Configurable
     */
    protected $configurable;
    /**
     * @var
     */
    protected $categoryCollection;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrencyInterFace;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonData;
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    protected $customerSession;

    protected $currencyFactory;

    protected $orderRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AttributeCollection $productAttributes
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Magento\Framework\Escaper $escaper
     * @param CollectionFactory $collectionFactory
     * @param Configurable $configurable
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManagerInterface
     * @param UserContextInterface $userContext
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AttributeCollection $productAttributes,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        Registry $registry,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Framework\Escaper $escaper,
        CollectionFactory $collectionFactory,
        Configurable $configurable,
        PriceCurrencyInterface $priceCurrency,
        ProductFactory $productFactory,
        \Magento\Framework\Json\Helper\Data $jsonData,
        ProductMetadataInterface $productMetadata,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManagerInterface,
        UserContextInterface $userContext,
        \Magento\Customer\Model\Session $session,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productAttributes = $productAttributes;
        $this->block = $blockFactory;
        $this->registry = $registry;
        $this->orderCollection = $orderCollectionFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->escaper = $escaper;
        $this->categoryCollection = $collectionFactory;
        $this->configurable = $configurable;
        $this->priceCurrencyInterFace = $priceCurrency;
        $this->productFactory = $productFactory;
        $this->jsonData = $jsonData;
        $this->productMetadata = $productMetadata;
        $this->cookieManager = $cookieManagerInterface;
        $this->userContext = $userContext;
        $this->customerSession = $session;
        $this->currencyFactory = $currencyFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getGAJSCode($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GA_JS_CODE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getGANonJSCode($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GA_NONJS_CODE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getImpressionChunkSize($storeId = null)
    {
        return $this->scopeConfig->getValue(self::IMPRESSION_CHUNK_SIZE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getProductIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue(self::PRODUCT_IDENTIFIER, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isProductBrand($storeId = null)
    {
        return $this->scopeConfig->getValue(self::PRODUCT_BRAND, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getProductBrandAttribute($storeId = null)
    {
        return $this->scopeConfig->getValue(self::PRODUCT_BRAND_ATTRIBUTE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnableVariant($storeId = null)
    {
        return $this->scopeConfig->getValue(self::ENABLE_VARIANT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getOrderSuccessTotalCalculation($storeId = null)
    {
        return $this->scopeConfig->getValue(self::ORDER_TOTAL_CALCULATION, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isExcludeTaxFromTransation($storeId = null)
    {
        return $this->scopeConfig->getValue(self::EXCLUDE_TAX_TRANSACTION, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isExcludeShipTransaction($storeId = null)
    {
        return $this->scopeConfig->getValue(self::EXCLUDE_SHIP_TRANSACTION, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isExcludeShippingIncludingTax($storeId = null)
    {
        return $this->scopeConfig->getValue(self::EXCLUDE_SHIP_INCLUDING_TAX, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getSuccessPagePath($storeId = null)
    {
        return $this->scopeConfig->getValue(self::SUCCESS_PATH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isExcludeOrderWithZero($storeId = null)
    {
        return $this->scopeConfig->getValue(self::EXCLUDE_ZERO_ORDER, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isMeasureProductLinks($storeId = null)
    {
        return $this->scopeConfig->getValue(self::MEASURE_PRODUCT_CLICK, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getChildParent($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CHILD_PARENT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getGTMApiAccountId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_ACCOUNT_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getGTMApiContainerId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_CONTAINER_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getGTMApiMeasurementId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_MEASUREMENT_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getGTMAPISecret($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_API_SECRET, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isCurlRequest($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_API_CURL, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getJsonExportPublicId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::JSON_EXPORT_PUBLIC_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isMoveJsBottom($storeId = null)
    {
        return $this->scopeConfig->getValue(self::IS_MOVE_JS_BOTTOM, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getConversionId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_API_CONVERSION_ID, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getConversionLabel($storeId = null)
    {
        return $this->scopeConfig->getValue(self::GTM_API_CONVERSION_LABEL, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getStoreName($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        try {
            return $this->productMetadata->getVersion();
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return null;
    }

    public function getLoggedInId()
    {
        try{
            return $this->userContext->getUserId();
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return null;
    }

    public function getLoginValue()
    {
        try{
            return $this->customerSession->getLoginVal();
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return null;
    }

    public function getSignInValue()
    {
        try{
            return $this->customerSession->getSignup();
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return null;
    }

    public function getCustomerSession(){
        return $this->customerSession;
    }

    /**
     * @param $product
     * @param int $index
     * @param string $list
     * @param null $categoryName
     * @return bool|string
     */
    public function addProductClick(
        $product,
        $index = 0,
        $list = null,
        $categoryName = null
    ) {
        $productClickBlock = $this->createBlock('Category', 'productClick.phtml');
        $html = '';

        if ($this->isMeasureProductLinks()) {
            if ($productClickBlock && $this->isEnable()) {
                $productClickBlock->setProduct($product);
                $productClickBlock->setIndex($index);
                $productClickBlock->setCategoryName($categoryName);

                if ($list != null) {
                    $currentCategory = $this->getCurrentCategory();
                    if (!empty($currentCategory)) {
                        $list = $this->getGA4Category($currentCategory);
                    } else {
                        $requestPath = $this->_request->getModuleName() .
                            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
                            DIRECTORY_SEPARATOR . $this->_request->getActionName();
                        switch ($requestPath) {
                            case 'catalogsearch/advanced/result':
                                $list = __('Advanced Search Result');
                                break;
                            case 'catalogsearch/result/index':
                                $list = __('Search Result');
                                break;
                        }
                    }
                }
                $productClickBlock->setList($list);
                // @codingStandardsIgnoreLine
                $html = trim($productClickBlock->toHtml());
            }

            if (!empty($html)) {
                $eventOnClick = ', "eventCallback": function() { document.location = "' .
                    $this->escaper->escapeHtml($product->getUrlModel()->getUrl($product)) . '";return false; }});';
                // @codingStandardsIgnoreLine
                $html = substr(rtrim($html, ");"), 0, -1);
                $eventOnClick = str_replace('"', "'", $eventOnClick);
                $html .= $eventOnClick;
            }
        }

        if (!empty($html)) {
            $html = 'onclick="' . $html . '"';
        }
        return $html;
    }

    /**
     * @param $category
     * @return string
     */
    public function getGA4Category($category)
    {
        $categoryPath = $category->getData('path');

        $categIds = explode('/', $categoryPath);
        $ignoreCategories = 2;
        if (sizeof($categIds) < 3) {
            $ignoreCategories = 1;
        }
        $categoryIds = array_slice($categIds, $ignoreCategories);

        $catNames = $this->getCategoryFromCategoryIds($categoryIds, 1);

        return implode('/', $catNames);
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return array
     */
    public function getProductAttribute()
    {
        try {
            $attributes = [];
            $attributeInfo = $this->productAttributes->create();
            foreach ($attributeInfo as $items) {
                array_push($attributes,
                    ['value' => $items->getData('attribute_code'), 'label' => $items->getData('attribute_code')]);
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
        return $attributes;
    }

    /**
     * @param $blockName
     * @param $template
     * @return bool
     */
    protected function createBlock($blockName, $template)
    {
        if ($block = $this->block->createBlock('\Meetanshi\GA4\Block\\' . $blockName)
            ->setTemplate('Meetanshi_GA4::' . $template)
        ) {
            return $block;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @param string $msg
     */
    public function logMessage($msg = '')
    {
        $this->_logger->info(print_r($msg, true));
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        try {
            //return $this->storeManager->getStore()->getCurrentCurrencyCode();
            return $this->storeManager->getStore()->getBaseCurrencyCode();
        }catch (\Magento\Framework\Exception\NoSuchEntityException $e){}
        return null;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseCurrencyCode()
    {
        return $this->storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getProductBrand($product)
    {
        return $product->getAttributeText($this->getProductBrandAttribute());
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getGA4ProductId($product)
    {
        $productSku = $product->getData('sku');
        if ($this->getProductIdentifier() == 1) {
            $productSku = $product->getData('product_id');
        }

        return $productSku;
    }

    /**
     * @param $categoryIds
     * @param int $multi
     * @return array|null
     */
    public function getCategoryFromCategoryIds($categoryIds, $multi = 0)
    {
        if ($categoryIds == null || sizeof($categoryIds) <= 0){
            return null;
        }

        $categoryFactory = $this->categoryCollection;
        $categories = $categoryFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => [$categoryIds]]);

        $multiCategoryArray = [];
        $categoryName = null;

        foreach ($categories as $category) {
            $categoryName = $category->getName();
            $multiCategoryArray[] = $categoryName;
        }

        if ($multi) {
            return $multiCategoryArray;
        }
        return $categoryName;
    }

    /**
     * @param $qty
     * @param $product
     * @param string $price
     * @return array
     * @throws \Zend_Log_Exception
     */
    public function addToCartData($qty, $product, $price = '')
    {
        $result = [];

        try {
            $result['event'] = 'add_to_cart';
            $result['ecommerce'] = [];
            $result['ecommerce']['currency'] = $this->getCurrencyCode();
            $result['ecommerce']['items'] = [];
            $productData = [];

            $options = $product->getTypeInstance(true)->getOrderOptions($product);
            $variant = '';
            $info = [];
            $option = [];

            if (isset($options['attributes_info'])) {
                $info = $options['attributes_info'];
            } elseif (isset($options['additional_info'])) {
                $info = $options['additional_info'];
            }elseif (isset($options['options'])){
                foreach ($options['options'] as $otp){
                    $option[] = $otp['value'];
                }
            }

            foreach ($info as $value) {
                $option[] = $value['value'];
                /*if ($variant != null) {
                    break;
                }*/
            }

            if (sizeof($option)){
                foreach ($option as $var){
                    if ($variant == '')
                        $variant = $var;
                    else
                        $variant = $variant . ',' . $var;
                }
            }

            $categoryIds = $product->getCategoryIds();
            $brand = null;

            $multiCategoryArray = $this->getCategoryData($categoryIds);

            $productSku = $product->getSku();
            if ($this->getProductIdentifier() == 1) {
                $productSku = $product->getId();
            }

            $productName = html_entity_decode($product->getName());

            if ($this->getChildParent() == 'parent') {
                if ($this->getParentProduct($product) != null) {
                    $parentProduct = $this->getParentProduct($product);
                    $productSku = $parentProduct->getData('sku');
                    $productName = html_entity_decode($parentProduct->getName());

                    if ($this->getProductIdentifier() == 1) {
                        $productSku = $parentProduct->getData('product_id');
                    }
                }
            }

            $brand = null;
            if ($this->isProductBrand()) {
                $attribute = $this->getProductBrandAttribute();
                if ($attribute != null)
                    $brand = $product->getAttributeText($attribute);
                if ($brand == null) {
                    $brand = $product->getData($attribute);
                }
                $productData['item_brand'] = $brand;
            }

            foreach ($multiCategoryArray as $key => $cat) {
                if ($key == 0) {
                    $productData["item_category"] = $cat;
                } else {
                    $j = $key + 1;
                    $productData["item_category$j"] = $cat;
                }
            }

            if ($price == '')
                $price = round($product->getPrice(), 2);

            $result['ecommerce']['value'] = $price;

            $productData['item_name'] = html_entity_decode($productName);
            $productData['item_id'] = $productSku;
            $productData['price'] = ($price/$qty);
            $productData['currency'] = $this->getCurrencyCode();

            if ($this->isProductBrand()) {
                $productData['item_brand'] = $brand;
            }

            $productCategoryIds = $product->getCategoryIds();
            $categoryName = $this->getCategoryFromCategoryIds($product->getCategoryIds());
            $productData['item_list_name'] = $categoryName;
            $productData['item_list_id'] = sizeof($productCategoryIds) ? $productCategoryIds[0] : 1;
            $productData['quantity'] = round($qty);
            $productData['currency'] = $this->getCurrencyCode();

            if ($this->isEnableVariant()) {
                if ($variant != null) {
                    $productData['item_variant'] = $variant;
                }
            }
            $result['ecommerce']['items'][] = $productData;
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }

        return $result;
    }

    /**
     * @param $product
     * @param $quoteItem
     * @param $qty
     * @param $price
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function removeCartData($product, $quoteItem, $qty, $price = '')
    {
        $options = $product->getTypeInstance(true)->getOrderOptions($product);
        $variant = '';
        $info = [];
        $result = [];

        $result['event'] = 'remove_from_cart';
        $result['ecommerce'] = [];
        $result['ecommerce']['currency'] = $this->getCurrencyCode();
        $result['ecommerce']['items'] = [];
        $productData = [];

        if (isset($options['attributes_info'])) {
            $info = $options['attributes_info'];
        } elseif (isset($options['additional_info'])) {
            $info = $options['additional_info'];
        }
        foreach ($info as $value) {
            $variant = $value['value'];
            if ($variant != null) {
                break;
            }
        }

        $categoryIds = $product->getCategoryIds();
        $brand = null;

        $multiCategoryArray = $this->getCategoryData($categoryIds);
        $productName = html_entity_decode($product->getName());

        if (!$this->getProductIdentifier()) {
            $productSku = $product->getSku();
        } else {
            $productSku = $product->getId();
        }

        if ($this->getChildParent() == 'parent') {
            if ($this->getParentProduct($product) != null) {
                $parentProduct = $this->getParentProduct($product);
                $productSku = $parentProduct->getData('sku');
                $productName = html_entity_decode($parentProduct->getName());

                if ($this->getProductIdentifier() == 1) {
                    $productSku = $parentProduct->getData('product_id');
                }
                $productData['item_id'] = $productSku;
            }
        }

        if ($price == '')
            $price = round($product->getPrice(), 2);

        $result['ecommerce']['value'] = $price;

        $brand = null;
        if ($this->isProductBrand()) {
            $attribute = $this->getProductBrandAttribute();
            if ($attribute != null)
                $brand = $product->getAttributeText($attribute);
            if ($brand == null) {
                $brand = $product->getData($attribute);
            }
            $productData['item_brand'] = $brand;
        }

        foreach ($multiCategoryArray as $key => $cat) {
            if ($key == 0) {
                $productData["item_category"] = $cat;
            } else {
                $j = $key + 1;
                $productData["item_category$j"] = $cat;
            }
        }

        $productData['item_name'] = $productName;
        $productData['item_id'] = $productSku;
        $productData['price'] = ($price/$qty);
        if ($this->isProductBrand()) {
            $productData['item_brand'] = $brand;
        }

        $productCategoryIds = $product->getCategoryIds();
        $categoryName = $this->getCategoryFromCategoryIds($product->getCategoryIds());
        $productData['item_list_name'] = $categoryName;
        $productData['item_list_id'] = sizeof($productCategoryIds) ? $productCategoryIds[0] : 1;
        $productData['quantity'] = round($qty);
        $productData['currency'] = $this->getCurrencyCode();

        if ($this->isEnableVariant() && $variant != null) {
            $productData['item_variant'] = $variant;
        }

        $result['ecommerce']['items'][] = $productData;

        return $result;
    }

    /**
     * @param $product
     * @param $wishListBuyRequest
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addToWishListData($product, $wishListBuyRequest)
    {
        $result = [];

        $result['event'] = 'add_to_wishlist';
        $result['ecommerce'] = [];
        $result['ecommerce']['currency'] = $this->getCurrencyCode();
        $result['ecommerce']['items'] = [];
        $productData = [];

        $categoryIds = $product->getCategoryIds();
        $brand = null;

        $multiCategoryArray = $this->getCategoryData($categoryIds);

        $productName = html_entity_decode($product->getName());

        if (!$this->getProductIdentifier()) {
            $productSku = $product->getSku();
        } else {
            $productSku = $product->getId();
        }

        if ($this->getChildParent() == 'parent') {
            if ($this->getParentProduct($product) != null) {
                $parentProduct = $this->getParentProduct($product);
                $productSku = $parentProduct->getData('sku');
                $productName = html_entity_decode($parentProduct->getName());

                if ($this->getProductIdentifier() == 1) {
                    $productSku = $parentProduct->getData('product_id');
                }
                $productData['item_id'] = $productSku;
            }
        }

        $price = $productData['price'] = round($product->getPrice(),2);

        $brand = null;
        if ($this->isProductBrand()) {
            $attribute = $this->getProductBrandAttribute();
            if ($attribute != null)
                $brand = $product->getAttributeText($attribute);
            if ($brand == null) {
                $brand = $product->getData($attribute);
            }
            $productData['item_brand'] = $brand;
        }

        foreach ($multiCategoryArray as $key => $cat) {
            if ($key == 0) {
                $productData['item_category'] = $cat;
            } else {
                $j = $key + 1;
                $productData['item_category'.$j] = $cat;
            }
        }

        $result['ecommerce']['value'] = (float)$price;

        $productData['item_name'] = $productName;
        $productData['item_id'] = $productSku;
        $productData['price'] = (float)$price;
        if ($this->isProductBrand()) {
            $productData['item_brand'] = $brand;
        }

        $productCategoryIds = $product->getCategoryIds();
        $categoryName = $this->getCategoryFromCategoryIds($product->getCategoryIds());
        $productData['item_list_name'] = $categoryName;
        $productData['item_list_id'] = sizeof($productCategoryIds) ? $productCategoryIds[0] : 1;

        if ($this->isEnableVariant()) {
            $productData['item_variant'] = $this->getVariantForProduct($product, $wishListBuyRequest);
        }

        $result['ecommerce']['items'][] = $productData;

        return $result;
    }

    /**
     * @param $product
     * @param $buyRequest
     * @return null|string
     */
    public function getVariantForProduct($product, $buyRequest)
    {
        $variant = [];

        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $options = $product->getTypeInstance(true)->getSelectedAttributesInfo($product);
            foreach ($options as $option) {
                $variant[] = $option['label'] . ": " . $option['value'];
            }

            if (!$variant && isset($buyRequest['super_attribute'])) {
                $superAttributeLabels = [];
                $superAttributeOptions = [];
                $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                foreach ($attributes as $attribute) {
                    $superAttributeLabels[$attribute['attribute_id']] = $attribute['label'];
                    foreach ($attribute->getOptions() as $option) {
                        $superAttributeOptions[$attribute['attribute_id']][$option['value_index']] = $option['store_label'];
                    }
                }

                foreach ($buyRequest['super_attribute'] as $key => $value) {
                    $variant[] = $superAttributeLabels[$key] . ": " . $superAttributeOptions[$key][$value];
                }
            }
        }

        if ($variant) {
            return implode(' | ', $variant);
        }
        return null;
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addToCompareData($product)
    {
        $result = [];

        $result['event'] = 'add_to_compare';
        $result['ecommerce'] = [];
        $result['ecommerce']['items'] = [];
        $productData = [];
        $productData['currency'] = $this->getCurrencyCode();

        $productName = html_entity_decode($product->getName());

        if (!$this->getProductIdentifier()) {
            $productSku = $product->getSku();
        } else {
            $productSku = $product->getId();
        }

        if ($this->getChildParent() == 'parent') {
            if ($this->getParentProduct($product) != null) {
                $parentProduct = $this->getParentProduct($product);
                $productSku = $parentProduct->getData('sku');
                $productName = html_entity_decode($parentProduct->getName());

                if ($this->getProductIdentifier() == 1) {
                    $productSku = $parentProduct->getData('product_id');
                }
                $productData['item_id'] = $productSku;
            }
        }

        $price = $productData['price'] = round($product->getPrice(), 2);

        $productData['item_name'] = $productName;
        $productData['item_id'] = $productSku;
        $productData['price'] = (float)$price;

        $productCategoryIds = $product->getCategoryIds();
        $categoryName = $this->getCategoryFromCategoryIds($product->getCategoryIds());
        $productData['item_list_id'] = sizeof($productCategoryIds) ? $productCategoryIds[0] : 1;
        $productData['item_list_name'] = $categoryName;

        $brand = null;
        if ($this->isProductBrand()) {
            $attribute = $this->getProductBrandAttribute();
            if ($attribute != null)
                $brand = $product->getAttributeText($attribute);
            if ($brand == null) {
                $brand = $product->getData($attribute);
            }
            $productData['item_brand'] = $brand;
        }

        $result['ecommerce']['items'][] = $productData;

        return $result;
    }

    /**
     * @param $product
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getParentProduct($product)
    {
        $parentId = null;
        $configProduct = $this->configurable->getParentIdsByChild($product->getId());
        if (isset($configProduct[0])) {
            $parentId = $configProduct[0];
        }
        if ($parentId != null) {
            $parentProduct = $this->productFactory->create($parentId);
            return $parentProduct;
        }
        return null;
    }

    /**
     * @param $categoryIds
     * @return array
     */
    public function getCategoryData($categoryIds)
    {
        if ($categoryIds == null || sizeof($categoryIds) <= 0){
            return [];
        }

        $multiCategoryArray = [];
        $categoryFactory = $this->categoryCollection;
        $categories = $categoryFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => [$categoryIds]]);

        foreach ($categories as $category) {
            $categoryName = $category->getName();
            $multiCategoryArray[] = $categoryName;
        }

        return $multiCategoryArray;
    }

    /**
     * @param $quoteId
     * @return string
     */
    public function getCheckoutCartData($quoteId)
    {
        $itemStr = $this->getQuoteItems($quoteId, 'cart_view', 1);
        $quote = $this->quoteFactory->create()->load($quoteId);
        $total = round($quote->getBaseGrandTotal(), 2);
        try {
            //$curCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $curCode = $this->getCurrencyCode();
        }catch (\Exception $e){$this->logMessage($e->getMessage()); $curCode = null;}
        $itemData = $this->jsonData->jsonEncode($itemStr);
        $couponCode = $quote->getCouponCode();
        if ($couponCode != null){
            $str = "window.dataLayer.push({ event: null }); window.dataLayer.push({ ecommerce: null }); window.dataLayer.push({event: 'view_cart', ecommerce: {currency: '$curCode', coupon: '$couponCode', value: $total, items: $itemData}});";
        }else {
            $str = "window.dataLayer.push({ ecommerce: null }); window.dataLayer.push({event: 'view_cart', ecommerce: {currency: '$curCode', value: $total, items: $itemData}});";
        }

        return $str;
    }

    /**
     * @param $listStr
     * @param $prodData
     * @return string
     */
    public function getListData($listStr, $prodData)
    {
        $categoryName = null;
        $categoryIds = $prodData['catids'];
        $categories = [];

        if ($categoryIds != null && sizeof($categoryIds) > 0) {
            $categoryFactory = $this->categoryCollection;
            $categories = $categoryFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', ['in' => [$categoryIds]]);
        }
        $productName = $prodData['name'];
        $productName = str_replace("'"," ", $productName);
        $productName = str_replace('"'," ", $productName);
        $productSku = $prodData['sku'];
        $productId = $prodData['id'];
        $price = round($prodData['price'], 2);
        $brand = null;
        if (isset($prodData['brand']))
            $brand = $prodData['brand'];

        $qty = round($prodData['qty']);

        if ($this->getProductIdentifier() == 1) {
            $productSku = $productId;
        }

        foreach ($categories as $category) {
            $categoryName = $category->getName();
            $multiCategoryArray[] = $categoryName;
        }

        if (sizeof($prodData)) {
            if ($listStr == '') {
                $listStr = "{
                        item_name: '$productName',
                        item_id: '$productSku',
                        price: $price,
                        quantity: $qty,
                        item_category: '$categoryName'";

                if ($this->isProductBrand() && $brand != null) {
                    $listStr .= ",\nitem_brand: '$brand'";
                }
                $listStr .= "\n}";
            } else {
                $listStr .= ", {
                        item_name: '$productName',
                        item_id: '$productSku',
                        price: $price,
                        quantity: $qty,
                        item_category: '$categoryName'";

                if ($this->isProductBrand() && $brand != null) {
                    $listStr .= ",\nitem_brand: '$brand'";
                }
                $listStr .= "\n}";
            }
        }

        return $listStr;
    }

    /**
     * @param $orderId
     * @param string $event
     * @param int $refund
     * @param array $refundData
     * @return array|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPurchaseData($orderId, $event = '', $refund = 0, $refundData = [])
    {
        $orders = $this->orderCollection->create()
            ->addFieldToSelect('*')
            ->addAttributeToFilter('entity_id', $orderId);

        $quoteId = null;
        $total = 0;
        $userData = [];
        if ($orders->count()) {
            $coupon = $transId = $storeName = $orderTax = $shipAmount = $currencyCode = null;
            foreach ($orders->getData() as $order) {
                $transId = $order['increment_id'];
                //$storeName = $order['store_name'];
                if ($this->getStoreName() != null) {
                    $storeName = $this->getStoreName();
                } else {
                    $storeName = $this->storeManager->getStore()->getName();
                }
                $currencyCode = $order['base_currency_code'];
                $grandTotal = round($order['base_grand_total'], 2);
                $subTotal = round($order['base_subtotal'], 2);
                $quoteId = $order['quote_id'];
                //$coupon = $order['coupon_rule_name'];
                $shipAmount = round($order['base_shipping_incl_tax'], 2);
                $orderTax = round($order['base_tax_amount'], 2);
                $shipTaxAmount = round($order['base_shipping_tax_amount'], 2);

                if ($this->getOrderSuccessTotalCalculation() == 'base_subtotal') {
                    $total = $subTotal;
                } else {
                    $total = $grandTotal;
                }
                if ($this->isExcludeShippingIncludingTax()) {
                    $shipAmount = $shipAmount - $shipTaxAmount;
                }

                $orderRepo = $this->orderRepository->get($order['entity_id']);
                $billAddress = $orderRepo->getBillingAddress();
                $coupon = $orderRepo->getCouponCode();

                if ($billAddress != null) {
                    $firstName = $billAddress->getData('firstname');
                    $lastName = $billAddress->getData('lastname');
                    $street = $billAddress->getData('street');
                    //$street = $orderRepo->getBillingAddress()->getStreet(1);
                    $city = $billAddress->getData('city');
                    $region = $billAddress->getData('region');
                    $postCode = $billAddress->getData('postcode');
                    $country = $this->getCountryName($billAddress->getData('country_id'));

                    $email = $billAddress->getData('email');
                    $email = hash('sha256', $email);

                    $telephone = $billAddress->getData('telephone');
                    $telephone = hash('sha256', $telephone);

                    if ($street != null && is_array($street) && isset($street[0]))
                        $street = $street[0];

                    $firstName = hash('sha256', $firstName);
                    $lastName = hash('sha256', $lastName);

                    $userData = [
                        'sha256_email_address' => "$email",
                        'sha256_phone_number' => "$telephone",
                        'address' => [
                            'sha256_first_name' => "$firstName",
                            'sha256_last_name' => "$lastName",
                            'street' => "$street",
                            'city' => "$city",
                            'region' => "$region",
                            'postal_code' => "$postCode",
                            'country' => "$country"
                        ]
                    ];
                }
            }

            //$shipAmount = (float)@number_format($shipAmount, 2);
            //$total = (float)@number_format($total, 2);

            if ($this->isExcludeOrderWithZero()) {
                if ($total <= 0) {
                    return '';
                }
            }

            $itemArray = [];
            if ($event == 'refund'){
                if ($refundData != null && sizeof($refundData)) {
                    $total = (float)$refundData['total'];
                    $shipAmount = $refundData['shipping'];
                    $orderTax = (float)$refundData['tax'];
                    $itemArray = $refundData['item'];
                }
            }else{
                $itemArray = $this->getItemsArrayData($quoteId);
            }

            if ($refund){
                $data = [
                    'event' => 'refund',
                    'ecommerce' => [
                        'transaction_id' => $transId,
                        'affiliation' => $storeName,
                        'value' => (float)$total,
                        'tax' => (float)$orderTax,
                        'shipping' => $shipAmount,
                        'currency' => $currencyCode
                    ]
                ];
            }else {
                $data = [
                    'event' => 'purchase',
                    'ecommerce' => [
                        'transaction_id' => $transId,
                        'affiliation' => $storeName,
                        'value' => (float)$total,
                        'tax' => (float)$orderTax,
                        'shipping' => $shipAmount,
                        'currency' => $currencyCode
                    ]
                ];
            }

            if ($userData != null && sizeof($userData)){
                $data['user_data'] = $userData;
            }
            if ($coupon != null && $coupon != '') {
                $data['ecommerce']['coupon'] = $coupon;
            }
            $data['ecommerce']['items'] = $itemArray;

            if ($this->isCurlRequest()) {
                $this->sendCurlReq($transId, $storeName, $total, $orderTax, $shipAmount, $currencyCode, $itemArray, $userData);
                return [];
            }
            return $data;
        }
        return [];
    }

    /**
     * @param $transId
     * @param $storeName
     * @param $total
     * @param $orderTax
     * @param $shipAmount
     * @param $currencyCode
     * @param $itemArray
     * @return int
     */
    public function sendCurlReq($transId, $storeName, $total, $orderTax, $shipAmount, $currencyCode, $itemArray, $userData = [])
    {
        try {
            $gaClient = $this->cookieManager->getCookie('_ga');
            $gaClient = str_replace('.', '', $gaClient);
            $customerId = $this->getLoggedInId();

            $curl = curl_init();

            $paramData = [
                "client_id" => "$gaClient",
                "events" => [
                    [
                        "name" => "purchase",
                        "params" => [
                            "transaction_id" => "$transId",
                            "affiliation" => "$storeName",
                            "value" => $total,
                            "tax" => $orderTax,
                            "shipping" => $shipAmount,
                            "currency" => "$currencyCode",
                            "items" => $itemArray,
                        ],
                    ],
                ],
            ];

            if ($customerId != null){
                $paramData["user_id"] = $customerId;
            }

            if ($userData != null && sizeof($userData)){
                $paramData["user_data"] = $userData;
            }

            $mesurementId = $this->getGTMApiMeasurementId();
            $apiSecret = $this->getGTMAPISecret();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.google-analytics.com/mp/collect?measurement_id=$mesurementId&api_secret=$apiSecret",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($paramData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            sleep(0.5);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            $this->logMessage('-- GA4 CURL --');
            $this->logMessage($transId);
            $this->logMessage($response);
            $this->logMessage($error);
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return 1;
    }

    /**
     * @param $quoteId
     * @return array
     */
    public function getItemsArrayData($quoteId)
    {
        $quote = $this->quoteFactory->create()->load($quoteId);
        $items = $quote->getAllVisibleItems();
        $isProductBrand = false;
        $multiCategoryArray = null;
        $tax = 0;
        if ($this->isProductBrand()) {
            $isProductBrand = true;
        }
        $brand = null;
        $itemData = null;
        $allItemData = null;

        foreach ($items as $item) {
            $parentItemId = $item->getParentItemId();
            $productName = $item->getName();
            $productSku = $item->getSku();
            $price = $productData['price'] = round($item->getBasePrice(),2);
            //$price = $productData['price'] = @number_format((float)$item->getProduct()->getPriceInfo()->getPrice('final_price')->getValue(),2, '.', '');

            $tax += round($item->getBaseTaxAmount(), 2);
            $qty = $item->getQty();

            if ($this->getProductIdentifier() == 1) {
                $productSku = $item->getProduct()->getId();
            }
            if ($this->getChildParent() == 'parent') {
                if ($parentItemId != null) {
                    $quoteItemCollection = $this->quoteItemCollectionFactory->create();
                    $quoteItem = $quoteItemCollection
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('item_id', $parentItemId)
                        ->getFirstItem();

                    $productSku = $quoteItem->getData('sku');
                    if ($this->getProductIdentifier() == 1) {
                        $productSku = $quoteItem->getData('product_id');
                    }
                }
            }

            $attribute = $this->getProductBrandAttribute();
            if ($isProductBrand && $attribute != null) {
                $brand = $item->getProduct()->getAttributeText($attribute);
                if ($brand == null) {
                    $brand = $item->getProduct()->getData($attribute);
                }
                if ($brand == null){
                    /** @var $product \Magento\Catalog\Model\Product */
                    $product = $this->productFactory->create()->load($item->getProduct()->getId());
                    $brand = $product->getAttributeText($attribute);
                    if ($brand == null){
                        $brand = $product->getData($attribute);
                    }
                }
            }

            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $info = [];
            $variant = '';

            if (isset($options['attributes_info'])) {
                $info = $options['attributes_info'];
            } elseif (isset($options['additional_info'])) {
                $info = $options['additional_info'];
            }
            foreach ($info as $value) {
                $variant = $value['value'];
                if ($variant != null) {
                    break;
                }
            }
            $categoryIds = $item->getProduct()->getCategoryIds();
            $categories = [];
            $categoryName = null;

            if ($categoryIds != null && sizeof($categoryIds) > 0) {
                $categoryFactory = $this->categoryCollection;
                $categories = $categoryFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', ['in' => [$categoryIds]]);
            }
            foreach ($categories as $category) {
                $categoryName = $category->getName();
                $multiCategoryArray[] = $categoryName;
            }
            $itemData['item_name'] = $productName;
            $itemData['item_id'] = $productSku;
            $itemData['price'] = $price;
            $itemData['quantity'] = round($qty);
            $itemData['discount'] = round($item->getDiscountAmount(), 2);
            $itemData['item_category'] = $categoryName;

            if ($this->isProductBrand() && $brand != null && $brand != " ") {
                $itemData['item_brand'] = $brand;
            }
            if ($this->isEnableVariant() && $variant != null) {
                $itemData['item_variant'] = $variant;
            }

            $productCategoryIds = $item->getProduct()->getCategoryIds();
            $categoryName = $this->getCategoryFromCategoryIds($item->getProduct()->getCategoryIds());
            $itemData['item_list_name'] = $categoryName;
            $itemData['item_list_id'] = sizeof($productCategoryIds) ? $productCategoryIds[0] : 1;

            $allItemData[] = $itemData;
            $itemData = null;
            $multiCategoryArray = [];
        }

        return $allItemData;
    }

    /**
     * @param $quoteId
     * @param $eventName
     * @param int $multiCategory
     * @param string $couponName
     * @param int $couponId
     * @return string|null|array
     */
    public function getQuoteItems($quoteId, $eventName, $multiCategory = 0, $couponName = '', $couponId = 0)
    {
        $quote = $this->quoteFactory->create()->load($quoteId);
        $items = $quote->getAllVisibleItems();
        $itemStr = '';
        $isProductBrand = false;
        $multiCategoryArray = [];
        $tax = 0;
        if ($this->isProductBrand()) {
            $isProductBrand = true;
        }
        $i = 0;
        $brand = null;
        $itemData = null;
        $allItemsData = null;

        foreach ($items as $item) {
            $parentItemId = $item->getParentItemId();
            $productName = $item->getName();
            $productSku = $item->getSku();
            $price = $productData['price'] = round($item->getBasePrice(), 2);
            //$price = $productData['price'] = @number_format((float)$item->getProduct()->getPriceInfo()->getPrice('final_price')->getValue(),2, '.', '');
            $tax += round($item->getTaxAmount(), 2);
            $qty = round($item->getQty());

            if ($this->getProductIdentifier() == 1) {
                $productSku = $item->getProduct()->getId();
            }
            if ($this->getChildParent() == 'parent') {
                if ($parentItemId != null) {
                    $quoteItemCollection = $this->quoteItemCollectionFactory->create();
                    $quoteItem = $quoteItemCollection
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('item_id', $parentItemId)
                        ->getFirstItem();

                    $productSku = $quoteItem->getData('sku');
                    if ($this->getProductIdentifier() == 1) {
                        $productSku = $quoteItem->getData('product_id');
                    }
                }
            }
            $attribute = $this->getProductBrandAttribute();
            if ($isProductBrand && $attribute != null) {
                $brand = $item->getProduct()->getAttributeText($attribute);
                if ($brand == null) {
                    $brand = $item->getProduct()->getData($attribute);
                }
                if ($brand == null){
                    /** @var $product \Magento\Catalog\Model\Product */
                    $product = $this->productFactory->create()->load($item->getProduct()->getId());
                    $brand = $product->getAttributeText($attribute);
                    if ($brand == null){
                        $brand = $product->getData($attribute);
                    }
                }
            }

            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $info = [];
            $variant = '';

            if (isset($options['attributes_info'])) {
                $info = $options['attributes_info'];
            } elseif (isset($options['additional_info'])) {
                $info = $options['additional_info'];
            }
            foreach ($info as $value) {
                $variant = $value['value'];
                if ($variant != null) {
                    break;
                }
            }
            $categoryIds = $item->getProduct()->getCategoryIds();
            $categories = [];
            $categoryName = null;

            if ($categoryIds != null && sizeof($categoryIds) > 0) {
                $categoryFactory = $this->categoryCollection;
                $categories = $categoryFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', ['in' => [$categoryIds]]);
            }
            foreach ($categories as $category) {
                $categoryName = $category->getName();
                $multiCategoryArray[] = $categoryName;
            }

            if ($i == 0 && $eventName == 'purchase') {
                $itemStr = "{
                        item_name: '$productName',
                        item_id: '$productSku',
                        price: $price,
                        quantity: $qty,
                        item_category: '$categoryName'";

                if ($this->isProductBrand() && $brand != null) {
                    $itemStr .= ",\nitem_brand: '$brand'";
                }
                if ($this->isEnableVariant() && $variant != null) {
                    $itemStr .= ",\nitem_variant: '$variant'";
                }
                $itemStr .= "\n}";
            } elseif ($eventName == 'purchase') {
                $itemStr .= ", {
                        item_name: '$productName',
                        item_id: '$productSku',
                        price: $price,
                        quantity: $qty,
                        item_category: '$categoryName'";

                if ($this->isProductBrand() && $brand != null) {
                    $itemStr .= ",\nitem_brand: '$brand'";
                }
                if ($this->isEnableVariant() && $variant != null) {
                    $itemStr .= ",\nitem_variant: '$variant'";
                }
                $itemStr .= "\n}";
            }

            if ($eventName == 'checkout') {
                $itemData['item_name'] = $productName;
                $itemData['item_id'] = $productSku;
                $itemData['price'] = $price;
                $itemData['discount'] = round($item->getDiscountAmount(), 2);

                if ($this->isProductBrand() && $brand != null) {
                    $itemData['item_brand'] = $brand;
                }

                foreach ($multiCategoryArray as $key => $cat) {
                    if ($key == 0) {
                        $itemData['item_category'] = $cat;
                    } else {
                        $j = $key + 1;
                        $itemData['item_category'.$j] = $cat;
                    }
                }

                if ($this->isEnableVariant() && $variant != null) {
                    $itemData['item_variant'] = $variant;
                }
                $itemData['quantity'] = $qty;
            }

            if ($eventName == 'coupon' || $eventName == 'cart_view') {
                $itemData['item_name'] = $productName;
                $itemData['item_id'] = $productSku;
                $itemData['price'] = $price;
                $itemData['discount'] = round($item->getDiscountAmount(), 2);

                if ($this->isProductBrand() && $brand != null) {
                    $itemData['item_brand'] = $brand;
                }

                foreach ($multiCategoryArray as $key => $cat) {
                    if ($key == 0) {
                        $itemData['item_category'] = $cat;
                    } else {
                        $j = $key + 1;
                        $itemData['item_category'.$j] = $cat;
                    }
                }

                if ($this->isEnableVariant() && $variant != null) {
                    $itemData['item_variant'] = $variant;
                }
                $itemData['quantity'] = $qty;

                if ($eventName == 'coupon') {
                    $itemData['promotion_id'] = $couponId;
                    $itemData['promotion_name'] = $couponName;
                }
            }

            $productCategoryIds = $item->getProduct()->getCategoryIds();
            $categoryName = $this->getCategoryFromCategoryIds($item->getProduct()->getCategoryIds());
            $itemData['item_list_id'] = sizeof($productCategoryIds) ? $productCategoryIds[0] : 1;
            $itemData['item_list_name'] = $categoryName;

            $allItemsData[] = $itemData;
            $itemData = null;
            $multiCategoryArray = [];
        }
        return $allItemsData;
    }

    /**
     * @param $cartId
     * @param $shipName
     * @param $total
     * @return array
     */
    public function getShippingInfo($cartId, $shipName, $total = 0.0)
    {
        $result = [];
        $result['event'] = 'add_shipping_info';
        $result['ecommerce'] = [];
        $result['ecommerce']['currency'] = $this->getCurrencyCode();
        $result['ecommerce']['value'] = round($total,2);
        //$result['ecommerce']['value'] = $total;
        $result['ecommerce']['shipping_tier'] = $shipName;

        $quote = $this->quoteFactory->create()->load($cartId);
        $couponCode = $quote->getCouponCode();
        if ($couponCode != null)
            $result['ecommerce']['coupon'] = $couponCode;

        $itemArray = $this->getItemsArrayData($cartId);
        $result['ecommerce']['items'] = $itemArray;
        return $result;
    }

    /**
     * @param $cartId
     * @param $code
     * @param $id
     * @return string
     */
    public function getCouponData($cartId, $code, $id)
    {
        $itemArray = $this->getQuoteItems($cartId, 'coupon', 0, $code, $id);
        $items = $this->jsonData->jsonEncode($itemArray);

        $str = "window.dataLayer.push({ ecommerce: null }); window.dataLayer.push({event: 'select_promotion',ecommerce: {items: $items}});";

        return $str;
    }

    /**
     * @param $cartId
     * @param $paymentName
     * @param $total
     * @return array
     */
    public function getPaymentInfo($cartId, $paymentName, $total = 0)
    {
        $result = [];
        $result['event'] = 'add_payment_info';
        $result['ecommerce'] = [];
        $result['ecommerce']['currency'] = $this->getCurrencyCode();

        $quote = $this->quoteFactory->create()->load($cartId);
        $couponCode = $quote->getCouponCode();
        if ($total == null || $total <= 0){
            $total = $quote->getBaseSubtotal();
        }
        $result['ecommerce']['value'] = $total;
        $result['ecommerce']['payment_type'] = $paymentName;

        if ($couponCode != null)
            $result['ecommerce']['coupon'] = $couponCode;

        $itemArray = $this->getItemsArrayData($cartId);
        $result['ecommerce']['items'] = $itemArray;
        return $result;
    }

    /**
     * @param $quoteId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCheckoutData($quoteId)
    {
        $itemStr = $this->getQuoteItems($quoteId, 'checkout', 1);
        $itemData = $this->jsonData->jsonEncode($itemStr);
        $quote = $this->quoteFactory->create()->load($quoteId);
        $total = $quote->getBaseGrandTotal();
        $couponCode = $quote->getCouponCode();
        $currency = $this->getCurrencyCode();

        if ($couponCode == null) {
            $str = "window.dataLayer.push({ event: null }); window.dataLayer.push({ ecommerce: null }); window.dataLayer.push({event: 'begin_checkout', ecommerce: {currency: '$currency',value: $total,items: $itemData}});";
        }else{
            $str = "window.dataLayer.push({ ecommerce: null }); window.dataLayer.push({event: 'begin_checkout', ecommerce: {currency: '$currency', coupon: '$couponCode', value: $total,items: $itemData}});";
        }

        return $str;
    }

    /**
     * @param $price
     * @return float|string
     */
    public function getCurrentStorePrice($price)
    {
        try {
            $priceCurrency = $this->priceCurrencyInterFace;
            if ($this->getCurrencyCode() != $this->getBaseCurrencyCode()) {
                return $priceCurrency->convert($price, $this->storeManager->getStore(), $this->getCurrencyCode());
            }
            return round($price, 2);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
        return '';
    }

    public function getBaseCurrencyPrice($price)
    {
        try {
            $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
            $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();

            $rate = $this->currencyFactory->create()->load($currentCurrency)->getAnyRate($baseCurrency);
            $baseValue = $price * $rate;

            return round($baseValue, 2);
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return $price;
    }

    public function getProductQty($product)
    {
        $qty = 0;
        $objManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            /** @var  \Magento\Framework\Module\Manager $moduleManager */
            $moduleManager = $objManager->get('Magento\Framework\Module\Manager');

            if ($moduleManager->isEnabled('Magento_InventorySalesApi')) {
                $productSalableQty = $objManager->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
                $websiteCode = $this->storeManager->getWebsite()->getCode();
                $stockResolver = $objManager->create(\Magento\InventorySalesApi\Api\StockResolverInterface::class);
                $stockId = $stockResolver->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $websiteCode)->getStockId();
                $qty = $productSalableQty->execute($product->getSku(), $stockId);
            } else {
                $stock = $product->getQuantityAndStockStatus();
                if (isset($stock['qty'])) {
                    $qty = $stock['qty'];
                }
            }
        }catch (\Exception $e){
            $this->logMessage($e->getMessage());
        }
        return $qty;
    }

    public function getCountryName($code)
    {
        $countries = [
            'AF' =>  'Afghanistan',
            'AL' =>  'Albania',
            'DZ' =>  'Algeria',
            'AS' =>  'American Samoa',
            'AD' =>  'Andorra',
            'AO' =>  'Angola',
            'AI' =>  'Anguilla',
            'AQ' =>  'Antarctica',
            'AG' =>  'Antigua and Barbuda',
            'AR' =>  'Argentina',
            'AM' =>  'Armenia',
            'AW' =>  'Aruba',
            'AU' =>  'Australia',
            'AT' =>  'Austria',
            'AZ' =>  'Azerbaijan',
            'BS' =>  'Bahamas',
            'BH' =>  'Bahrain',
            'BD' =>  'Bangladesh',
            'BB' =>  'Barbados',
            'BY' =>  'Belarus',
            'BE' =>  'Belgium',
            'BZ' =>  'Belize',
            'BJ' =>  'Benin',
            'BM' =>  'Bermuda',
            'BT' =>  'Bhutan',
            'BO' =>  'Bolivia',
            'BA' =>  'Bosnia and Herzegovina',
            'BW' =>  'Botswana',
            'BV' =>  'Bouvet Island',
            'BR' =>  'Brazil',
            'BQ' =>  'British Antarctic Territory',
            'IO' =>  'British Indian Ocean Territory',
            'VG' =>  'British Virgin Islands',
            'BN' =>  'Brunei',
            'BG' =>  'Bulgaria',
            'BF' =>  'Burkina Faso',
            'BI' =>  'Burundi',
            'KH' =>  'Cambodia',
            'CM' =>  'Cameroon',
            'CA' =>  'Canada',
            'CT' =>  'Canton and Enderbury Islands',
            'CV' =>  'Cape Verde',
            'KY' =>  'Cayman Islands',
            'CF' =>  'Central African Republic',
            'TD' =>  'Chad',
            'CL' =>  'Chile',
            'CN' =>  'China',
            'CX' =>  'Christmas Island',
            'CC' =>  'Cocos [Keeling] Islands',
            'CO' =>  'Colombia',
            'KM' =>  'Comoros',
            'CG' =>  'Congo - Brazzaville',
            'CD' =>  'Congo - Kinshasa',
            'CK' =>  'Cook Islands',
            'CR' =>  'Costa Rica',
            'HR' =>  'Croatia',
            'CU' =>  'Cuba',
            'CY' =>  'Cyprus',
            'CZ' =>  'Czech Republic',
            'CI' =>  'Côte d’Ivoire',
            'DK' =>  'Denmark',
            'DJ' =>  'Djibouti',
            'DM' =>  'Dominica',
            'DO' =>  'Dominican Republic',
            'NQ' =>  'Dronning Maud Land',
            'DD' =>  'East Germany',
            'EC' =>  'Ecuador',
            'EG' =>  'Egypt',
            'SV' =>  'El Salvador',
            'GQ' =>  'Equatorial Guinea',
            'ER' =>  'Eritrea',
            'EE' =>  'Estonia',
            'ET' =>  'Ethiopia',
            'FK' =>  'Falkland Islands',
            'FO' =>  'Faroe Islands',
            'FJ' =>  'Fiji',
            'FI' =>  'Finland',
            'FR' =>  'France',
            'GF' =>  'French Guiana',
            'PF' =>  'French Polynesia',
            'TF' =>  'French Southern Territories',
            'FQ' =>  'French Southern and Antarctic Territories',
            'GA' =>  'Gabon',
            'GM' =>  'Gambia',
            'GE' =>  'Georgia',
            'DE' =>  'Germany',
            'GH' =>  'Ghana',
            'GI' =>  'Gibraltar',
            'GR' =>  'Greece',
            'GL' =>  'Greenland',
            'GD' =>  'Grenada',
            'GP' =>  'Guadeloupe',
            'GU' =>  'Guam',
            'GT' =>  'Guatemala',
            'GG' =>  'Guernsey',
            'GN' =>  'Guinea',
            'GW' =>  'Guinea-Bissau',
            'GY' =>  'Guyana',
            'HT' =>  'Haiti',
            'HM' =>  'Heard Island and McDonald Islands',
            'HN' =>  'Honduras',
            'HK' =>  'Hong Kong SAR China',
            'HU' =>  'Hungary',
            'IS' =>  'Iceland',
            'IN' =>  'India',
            'ID' =>  'Indonesia',
            'IR' =>  'Iran',
            'IQ' =>  'Iraq',
            'IE' =>  'Ireland',
            'IM' =>  'Isle of Man',
            'IL' =>  'Israel',
            'IT' =>  'Italy',
            'JM' =>  'Jamaica',
            'JP' =>  'Japan',
            'JE' =>  'Jersey',
            'JT' =>  'Johnston Island',
            'JO' =>  'Jordan',
            'KZ' =>  'Kazakhstan',
            'KE' =>  'Kenya',
            'KI' =>  'Kiribati',
            'KW' =>  'Kuwait',
            'KG' =>  'Kyrgyzstan',
            'LA' =>  'Laos',
            'LV' =>  'Latvia',
            'LB' =>  'Lebanon',
            'LS' =>  'Lesotho',
            'LR' =>  'Liberia',
            'LY' =>  'Libya',
            'LI' =>  'Liechtenstein',
            'LT' =>  'Lithuania',
            'LU' =>  'Luxembourg',
            'MO' =>  'Macau SAR China',
            'MK' =>  'Macedonia',
            'MG' =>  'Madagascar',
            'MW' =>  'Malawi',
            'MY' =>  'Malaysia',
            'MV' =>  'Maldives',
            'ML' =>  'Mali',
            'MT' =>  'Malta',
            'MH' =>  'Marshall Islands',
            'MQ' =>  'Martinique',
            'MR' =>  'Mauritania',
            'MU' =>  'Mauritius',
            'YT' =>  'Mayotte',
            'FX' =>  'Metropolitan France',
            'MX' =>  'Mexico',
            'FM' =>  'Micronesia',
            'MI' =>  'Midway Islands',
            'MD' =>  'Moldova',
            'MC' =>  'Monaco',
            'MN' =>  'Mongolia',
            'ME' =>  'Montenegro',
            'MS' =>  'Montserrat',
            'MA' =>  'Morocco',
            'MZ' =>  'Mozambique',
            'MM' =>  'Myanmar [Burma]',
            'NA' =>  'Namibia',
            'NR' =>  'Nauru',
            'NP' =>  'Nepal',
            'NL' =>  'Netherlands',
            'AN' =>  'Netherlands Antilles',
            'NT' =>  'Neutral Zone',
            'NC' =>  'New Caledonia',
            'NZ' =>  'New Zealand',
            'NI' =>  'Nicaragua',
            'NE' =>  'Niger',
            'NG' =>  'Nigeria',
            'NU' =>  'Niue',
            'NF' =>  'Norfolk Island',
            'KP' =>  'North Korea',
            'VD' =>  'North Vietnam',
            'MP' =>  'Northern Mariana Islands',
            'NO' =>  'Norway',
            'OM' =>  'Oman',
            'PC' =>  'Pacific Islands Trust Territory',
            'PK' =>  'Pakistan',
            'PW' =>  'Palau',
            'PS' =>  'Palestinian Territories',
            'PA' =>  'Panama',
            'PZ' =>  'Panama Canal Zone',
            'PG' =>  'Papua New Guinea',
            'PY' =>  'Paraguay',
            'YD' =>  'People\'s Democratic Republic of Yemen',
            'PE' =>  'Peru',
            'PH' =>  'Philippines',
            'PN' =>  'Pitcairn Islands',
            'PL' =>  'Poland',
            'PT' =>  'Portugal',
            'PR' =>  'Puerto Rico',
            'QA' =>  'Qatar',
            'RO' =>  'Romania',
            'RU' =>  'Russia',
            'RW' =>  'Rwanda',
            'BL' =>  'Saint Barthélemy',
            'SH' =>  'Saint Helena',
            'KN' =>  'Saint Kitts and Nevis',
            'LC' =>  'Saint Lucia',
            'MF' =>  'Saint Martin',
            'PM' =>  'Saint Pierre and Miquelon',
            'VC' =>  'Saint Vincent and the Grenadines',
            'WS' =>  'Samoa',
            'SM' =>  'San Marino',
            'SA' =>  'Saudi Arabia',
            'SN' =>  'Senegal',
            'RS' =>  'Serbia',
            'CS' =>  'Serbia and Montenegro',
            'SC' =>  'Seychelles',
            'SL' =>  'Sierra Leone',
            'SG' =>  'Singapore',
            'SK' =>  'Slovakia',
            'SI' =>  'Slovenia',
            'SB' =>  'Solomon Islands',
            'SO' =>  'Somalia',
            'ZA' =>  'South Africa',
            'GS' =>  'South Georgia and the South Sandwich Islands',
            'KR' =>  'South Korea',
            'ES' =>  'Spain',
            'LK' =>  'Sri Lanka',
            'SD' =>  'Sudan',
            'SR' =>  'Suriname',
            'SJ' =>  'Svalbard and Jan Mayen',
            'SZ' =>  'Swaziland',
            'SE' =>  'Sweden',
            'CH' =>  'Switzerland',
            'SY' =>  'Syria',
            'ST' =>  'São Tomé and Príncipe',
            'TW' =>  'Taiwan',
            'TJ' =>  'Tajikistan',
            'TZ' =>  'Tanzania',
            'TH' =>  'Thailand',
            'TL' =>  'Timor-Leste',
            'TG' =>  'Togo',
            'TK' =>  'Tokelau',
            'TO' =>  'Tonga',
            'TT' =>  'Trinidad and Tobago',
            'TN' =>  'Tunisia',
            'TR' =>  'Turkey',
            'TM' =>  'Turkmenistan',
            'TC' =>  'Turks and Caicos Islands',
            'TV' =>  'Tuvalu',
            'UM' =>  'U.S. Minor Outlying Islands',
            'PU' =>  'U.S. Miscellaneous Pacific Islands',
            'VI' =>  'U.S. Virgin Islands',
            'UG' =>  'Uganda',
            'UA' =>  'Ukraine',
            'SU' =>  'Union of Soviet Socialist Republics',
            'AE' =>  'United Arab Emirates',
            'GB' =>  'United Kingdom',
            'US' =>  'United States',
            'ZZ' =>  'Unknown or Invalid Region',
            'UY' =>  'Uruguay',
            'UZ' =>  'Uzbekistan',
            'VU' =>  'Vanuatu',
            'VA' =>  'Vatican City',
            'VE' =>  'Venezuela',
            'VN' =>  'Vietnam',
            'WK' =>  'Wake Island',
            'WF' =>  'Wallis and Futuna',
            'EH' =>  'Western Sahara',
            'YE' =>  'Yemen',
            'ZM' =>  'Zambia',
            'ZW' =>  'Zimbabwe',
            'AX' =>  'Åland Islands',
        ];

        if (isset($countries[$code]))
            return $countries[$code];

        return $code;
    }
}