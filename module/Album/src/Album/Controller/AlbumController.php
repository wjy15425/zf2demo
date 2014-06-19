<?php
namespace Album\Controller;

use Zend\Mvc\controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;

class AlbumController extends AbstractActionController{
    protected $albumTable;
    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }
    public function addAction()
    {
    	$form = new AlbumForm();
    	$form->get('submit')->setValue('Add');
    	
    	$request = $this->getRequest();
    	if($request->isPost()) {
    		$album = new Album();
    		$form->setInputFilter($album->getInputFilter());
    		$form->setData($request->getPost());
    		
    		if($form->isValid()) {
    			$album->exchangeArray($form->getData());
    			$this->getAlbumTable()->saveAlbum($album);
    			
    			return $this->redirect()->toRoute('halbum');
    		}
    	}
    	return array(
    			'form' => $form,
    	);
    }
    public function editAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if(!$id) {
    		return $this->redirect()->toRoute('halbum/default', array(
    			'action' => 'add'
    		));
    	}
    	
    	try {
    		$album = $this->getAlbumTable()->getAlbum($id);
    	}
    	catch (\Exception $ex) {
    		return $this->redirect()->toRoute('halbum/default', array(
    				'action' => 'index'
    		));
    	}
    	//var_dump($album);die;
    	$form  = new AlbumForm();
    	$form->bind($album);
    	$form->get('submit')->setAttribute('value', 'Save');
    	
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($album->getInputFilter());
    		$form->setData($request->getPost());
    	
    		if ($form->isValid()) {
    			$this->getAlbumTable()->saveAlbum($album);
    	
    			// Redirect to list of albums
    			return $this->redirect()->toRoute('halbum');
    		}
    	}
    	
    	return array(
    			'id' => $id,
    			'form' => $form,
    	);
    }
    public function infoAction()
    {
    	$id = $this->params()->fromRoute('id', 0);
    	if(!$id) {
    		return $this->redirect()->toRoute('halbum');
    	}
    	
    	try {
    		$album = $this->getAlbumTable()->getAlbum($id);
    	} catch (\Exception $e) {
    		$this->redirect()->toRoute('halbum');
    	}
    	
        return array(
        		'album' => $album,
        );
    }
    public function deleteAction()
    {
    	echo __FUNCTION__;die;
    }
}
