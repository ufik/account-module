<?php

namespace WebCMS\AccountModule\Doctrine;

use Doctrine\ORM\Mapping as orm;

/**
 * Description of Account
 * @orm\Entity
 * @orm\Table(name="accountModule")
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Account extends \AdminModule\Doctrine\Entity {
	/**
	 * @orm\Column
	 */
	private $firstname;
	
	/**
	 * @orm\Column
	 */
	private $lastname;
	
	/**
	 * @orm\Column
	 */
	private $street;
	
	/**
	 * @orm\Column
	 */
	private $city;
	
	/**
	 * @orm\Column(type="integer")
	 */
	private $postcode;
	
	/**
	 * @orm\Column
	 */
	private $state;
	
	/**
	 * @orm\Column
	 */
	private $email;
	
	/**
	 * @orm\Column
	 */
	private $phone;
	
	/**
	 * @orm\Column
	 */
	private $invoiceCompany;
	
	/**
	 * @orm\Column
	 */
	private $invoiceNo;
	
	/**
	 * @orm\Column
	 */
	private $invoiceVatNo;
	
	/**
	 * @orm\Column
	 */
	private $invoiceStreet;
	
	/**
	 * @orm\Column
	 */
	private $invoiceCity;
	
	/**
	 * @orm\Column(type="integer")
	 */
	private $invoicePostcode;
	
	/**
	 * @orm\Column
	 */
	private $invoiceState;
	
	public function getFirstname() {
		return $this->firstname;
	}

	public function getLastname() {
		return $this->lastname;
	}

	public function getStreet() {
		return $this->street;
	}

	public function getCity() {
		return $this->city;
	}

	public function getPostcode() {
		return $this->postcode;
	}

	public function getState() {
		return $this->state;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function getInvoiceCompany() {
		return $this->invoiceCompany;
	}

	public function getInvoiceNo() {
		return $this->invoiceNo;
	}

	public function getInvoiceVatNo() {
		return $this->invoiceVatNo;
	}

	public function getInvoiceStreet() {
		return $this->invoiceStreet;
	}

	public function getInvoiceCity() {
		return $this->invoiceCity;
	}

	public function getInvoicePostcode() {
		return $this->invoicePostcode;
	}

	public function getInvoiceState() {
		return $this->invoiceState;
	}

	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}

	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	public function setStreet($street) {
		$this->street = $street;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	public function setPostcode($postcode) {
		$this->postcode = $postcode;
	}

	public function setState($state) {
		$this->state = $state;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
	}

	public function setInvoiceCompany($invoiceCompany) {
		$this->invoiceCompany = $invoiceCompany;
	}

	public function setInvoiceNo($invoiceNo) {
		$this->invoiceNo = $invoiceNo;
	}

	public function setInvoiceVatNo($invoiceVatNo) {
		$this->invoiceVatNo = $invoiceVatNo;
	}

	public function setInvoiceStreet($invoiceStreet) {
		$this->invoiceStreet = $invoiceStreet;
	}

	public function setInvoiceCity($invoiceCity) {
		$this->invoiceCity = $invoiceCity;
	}

	public function setInvoicePostcode($invoicePostcode) {
		$this->invoicePostcode = $invoicePostcode;
	}

	public function setInvoiceState($invoiceState) {
		$this->invoiceState = $invoiceState;
	}
}