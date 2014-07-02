<?php
namespace Checklist\Model;

class TaskEntity
{
	protected $id;
	protected $title;
	protected $completed;
	protected $created;
	protected $updated;
	
	function __construct()
	{
		$this->updated = date('Y-m-d H:i:s');
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function setId($id)
	{
		$this->id = $id;
	}
	
	function getTitle()
	{
		return $this->title;
	}
	
	function setTitle($title)
	{
		$this->title = $title;
	}
	
	function getCompleted()
	{
		return $this->completed;
	}
	
	function setCompleted($completed)
	{
		$this->completed = $completed;
	}
	
	function getCreated()
	{
		return $this->created;
	}
	
	function setCreated($created)
	{
		$this->created = $created;
	}
	
	function getUpdated()
	{
		return $this->updated;
	}
	
	function setUpdated($updated)
	{
		$this->updated = $updated;
	}
}