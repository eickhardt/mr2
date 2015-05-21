<?php

/**
 * Check if we are running from inside Prestashop.
 */
if (!defined('_PS_VERSION_'))
    exit;

/**
 * Import the Fotolia API
 */
require_once __DIR__.'/vendor/fotolia-api.php';

/**
 * The main module class.
 */
class AzFotolia extends Module
{
    /**
     * The constructor is called whenever an instance of AZFotolia is created.
     */
    public function __construct()
    {
        $this->name = 'azfotolia';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Azenty - Daniel Eickhardt';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fotolia Library');
        $this->description = $this->l('Allows end-users to choose images for the cutting tool.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('AZ_FOTOLIA_NAME'))
            $this->warning = $this->l('No name provided');
    }

    /**
     * This function is called when the store administrator installs this module.
     * @return bool Weather or not the module was successfully installed.
     */
    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);
     
        if (!parent::install() ||
          !$this->registerHook('leftColumn') ||
          !$this->registerHook('header') ||
          !$this->registerHook('displayHome') ||
          !Configuration::updateValue('AZ_FOTOLIA_NAME', 'Fotolia'))
            return false;
        return true;
    }

    /**
     * Function that is called when the shop admin uninstalls this mod.
     * @return bool Weather or not the module was succesfully uninstalled.
     */
    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('AZ_FOTOLIA_NAME'))
            return false;
        return true;
    }

    /**
     * Collect and display data for hookDisaplyLeftColumn.
     * @param  array $params Parameters to consider
     * @return String        Generated HTML
     */
    public function hookDisplayLeftColumn($params)
    {
        // Instatiate Fotolia API
        $fotolia_api = new Fotolia_Api('J0ZhzOqFs8z4h5YfsDhZWEGUDibMgQAq');
        
        // Search for files
        $results = $fotolia_api->getSearchResults(
            array(
                'words' => 'car',
                'language_id' => Fotolia_Api::LANGUAGE_ID_EN_US,
                'limit' => 5,
            )
        );
        
        // Assign variables for the smarty templating engine
        $this->context->smarty->assign(array(
            'my_module_name' => Configuration::get('AZ_FOTOLIA_NAME'),
            'my_module_link' => $this->context->link->getModuleLink('azfotolia', 'display'),
            'my_module_message' => $this->l('This is a simple text message.'),
            'path' => '<span class="navigation_end">'. $this->l('Fotolia') .'</span>',
            'results' => $results

        ));
        return $this->display(__FILE__, 'azfotolia.tpl');
    }
    
    /**
     * Collect data and display HTML for hookDisplayRightColumn.
     * @param  array $params Parameters to consider
     * @return String        Generated HTML
     */
    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    /**
     * Collect data and display HTML for hookDisplayRightColumn.
     * @param  array $params Parameters to consider
     * @return String        Generated HTML
     */
    public function hookDisplayHome($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }
    
    /**
     * Adds CSS to the header using hookDisplayHeader.
     * @return void
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'css/azfotolia.css', 'all');
    }

    /**
     * This method is used to tell Prestashop that we want a configuration page.
     * @return String Configuration page form HTML
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $az_fotolia_name = strval(Tools::getValue('AZ_FOTOLIA_NAME'));
            if (!$az_fotolia_name
              || empty($az_fotolia_name)
              || !Validate::isGenericName($az_fotolia_name))
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else {
                Configuration::updateValue('AZ_FOTOLIA_NAME', $az_fotolia_name);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

    /**
     * Builds and returns the HTML for the configuration form that belongs to this module.
     * @return String Form HTML
     */
    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
         
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Configuration value'),
                    'name' => 'AZ_FOTOLIA_NAME',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
         
        $helper = new HelperForm();
         
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
         
        // Load current value
        $helper->fields_value['AZ_FOTOLIA_NAME'] = Configuration::get('AZ_FOTOLIA_NAME');
         
        return $helper->generateForm($fields_form);
    }
}
