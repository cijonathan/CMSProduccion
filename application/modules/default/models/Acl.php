<?php

class Model_Acl  extends Zend_Acl
{
    public function __construct() 
    {
        # [ROLES]
        $this->addRole(new Zend_Acl_Role('visitante'));
        $this->addRole(new Zend_Acl_Role('basico'),array('visitante'));             
        $this->addRole(new Zend_Acl_Role('administrador'),array('visitante','basico'));             
        
        # [RECURSOS]          
        $this->add(new Zend_Acl_Resource('default'));
        $this->add(new Zend_Acl_Resource('default:index'));
        $this->add(new Zend_Acl_Resource('default:feed'));
        $this->add(new Zend_Acl_Resource('default:error'));        
        $this->add(new Zend_Acl_Resource('default:modulo'));        
        $this->add(new Zend_Acl_Resource('default:perfil'));        
        $this->add(new Zend_Acl_Resource('default:ayuda'));        
        $this->add(new Zend_Acl_Resource('default:configuracion'));        
        $this->add(new Zend_Acl_Resource('default:tablero'));         
        $this->add(new Zend_Acl_Resource('default:usuario'));  
      
        # [PERMISOS]
        $this->deny('visitante');
        $this->allow('visitante',array('default:index','default:error','default:feed'));        
        $this->deny('basico',array('default:usuario'));
        $this->allow('basico',array('default:tablero','default:modulo','default:perfil','default:ayuda'));
        $this->allow('administrador',array('default:usuario','default:configuracion'));        
    }
}