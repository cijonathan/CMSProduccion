<?php

class FeedController extends Zend_Controller_Action
{
    protected $id_modulo;

    public function init(){
        $this->id_modulo = $this->_getParam('id',0);
    }

    public function indexAction(){
        /* [DESAHIBILITAR LAYOUT y VIEW] */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);        
        /* OBTENER DATOS */
        $modulo = new Default_Model_DbTable_Modulo();
        $datosmodulo = $modulo->obtenerdato($this->id_modulo); 
        $proyecto = new Default_Model_DbTable_Proyecto();
        $datosproyecto = $proyecto->obtener($datosmodulo->id_empresa);
        /* CUERPO PRINCIPAL */
        $cuerpo = array(
            'title'=>'Channel Title',
            'description'=>'Channel Description here',
            'link'=>'http://www.example.com',
            'charset'=>'utf8',
            'entries'=>array(
                array(
                    'title'=>'Article 1',
                    'pubDate'=>'Sun, 29 Apr 2012 03:14:35 +0000',
                    'description'=>'Article 1 contents goes here',
                    'link'=>'http://www.example.com/article-1',
                    'guid'=>'1A'
                ),
                array(
                    'title'=>'Article 2',
                    'description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'link'=>'http://www.example.com/article-2',
                    'guid'=>'2A'
                )
            )
        );  
        /*echo '<pre>';
        print_r($cuerpo); exit;*/
        $feed = Zend_Feed::importArray($cuerpo, 'rss');        
        header ( 'Content-type: text/xml' );        
        echo $feed->send();
        
    }
}

