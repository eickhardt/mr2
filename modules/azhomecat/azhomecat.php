<?php

/**
 * Check if we are running from inside Prestashop.
 */
if (!defined('_PS_VERSION_'))
    exit;

/**
 * The main module class.
 */
class AzHomeCat extends Module
{
    /**
     * The constructor is called whenever an instance of BlockDFM is created.
     */
    public function __construct()
    {
        $this->name = 'azhomecat';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Azenty - Daniel Eickhardt';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Az Homepage Categories');
        $this->description = $this->l('Small mod that allows for categories to be shown on the home page, in the top column.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('AZ_HOME_CAT_NAME'))
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
          !$this->registerHook('displayTopColumn') ||
          !$this->registerHook('header') ||
          !Configuration::updateValue('AZ_HOME_CAT_NAME', 'Az Homepage Categories'))
            return false;
        return true;
    }

    /**
     * Function that is called when the shop admin uninstalls this mod.
     * @return bool Weather or not the module was succesfully uninstalled.
     */
    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('AZ_HOME_CAT_NAME'))
            return false;
        return true;
    }

    /**
     * Collect and display data for hookDisaplyLeftColumn.
     * @param  array $params Parameters to consider
     * @return String        Generated HTML
     */
    public function hookDisplayTopColumn($params)
    {
        $this->context->smarty->assign(array(
            'my_module_name' => Configuration::get('AZ_HOME_CAT_NAME')
        ));
        return $this->display(__FILE__, 'azhomecat.tpl');
    }

    /**
     * Adds CSS to the header using hookDisplayHeader.
     * @return void
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'css/azhomecat.css', 'all');
    }

    /**
     * This method is used to tell Prestashop that we want a configuration page.
     * @return String Configuration page form HTML
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $dfm_name = strval(Tools::getValue('AZ_HOME_CAT_NAME'));
            if (!$dfm_name
              || empty($dfm_name)
              || !Validate::isGenericName($dfm_name))
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else {
                Configuration::updateValue('AZ_HOME_CAT_NAME', $dfm_name);
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
                    'name' => 'AZ_HOME_CAT_NAME',
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
        $helper->fields_value['AZ_HOME_CAT_NAME'] = Configuration::get('AZ_HOME_CAT_NAME');
         
        return $helper->generateForm($fields_form);
    }
}
