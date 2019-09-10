<?php


abstract class WebAppPermission
{
	/**
    * Name of the webApp Permission
    **/
    private $_webAppPermission_name;
	
	private $_webAppMenus;
	
	/**
     * Constuct a WebAppPermission
     * @param $permission_name    name of the permission
     */
    public function __construct($permission_name)
    {
        if($permission_name!=NULL && $permission_name!=""){
			$this->_webAppPermission_name = $permission_name;
		}
		else
		{
			throw new Exception("The Permission's name can't be NULL");
		}
		$this->webAppMenus = array();
    }
	
	/**
     * @param $permission_name name of the WebAppRole
     */
    public function setWebAppPermissionName($permission_name)
    {
        if(isset($permission_name) && $role_name!=NULL && $permission_name!=""){
			$this->_webAppPermission_name = $permission_name;
		}
		else{
			throw new Exception("The Permission's name can't be NULL");
		}
    }	
	
	public function getWebAppPermissionName()
	{
		return  $this->_webAppPermission_name;
	}
	
	public function addWebAppMenu($menu){
		if($menu!=NULL){
			$this->webAppMenus[$menu->getIdentifier()]=$menu;
		}
	}
	
	public function getIdentifier()
	{
		if(isset($this) && $this->getWebAppPermissionName()!=NULL){
			return $this->getDatabaseId();
		}
		else
		{
			return null;
		}
	}

	
	
	//public abstract static function getAllPermissionsFromRoleId($role_id);
	
	abstract protected function getDatabaseId();
	
}