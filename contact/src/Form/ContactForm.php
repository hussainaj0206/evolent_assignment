<?php

/**
 * @file 
 * Contains Drupal\contact\Form\ContactForm
 *
 */

namespace Drupal\contact\Form;
    
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Class ContactForm.
 *
 * @package Drupal\contact\Form
 */

class ContactForm extends FormBase {
    /**
    * {@inheritdoc}
    * Setting the form id
    */
    public function getFormId() {
        return 'contact_form';
    }

    /**
    * {@inheritdoc}
    * Creating the form fields
    */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $conn = Database::getConnection();
        $record = array();
        if (isset($_GET['id'])) {
            $query = $conn->select('user_contact_details', 'ucd')
                            ->condition('id', $_GET['id'])
                            ->fields('ucd');
            $record = $query->execute()->fetchAssoc();
        }

        //print_r($record);die;
        $form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => t('First Name'),
            '#required' => TRUE,
            '#default_value' => (isset($record['first_name']) && $_GET['id']) ? $record['first_name']:'',
        );

        $form['last_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Last Name'),
            '#required' => TRUE,
            '#default_value' => (isset($record['last_name']) && $_GET['id']) ? $record['last_name']:'',
        );

        $form['email'] = array(
            '#type' => 'email',
            '#title' => t('Email'),
            '#required' => TRUE,
            '#default_value' => (isset($record['email']) && $_GET['id']) ? $record['email']:'',
        );
        
        $form['contact_no'] = array (
            '#type' => 'tel',
            '#title' => t('Phone Number'),
            '#required' => TRUE,
            '#default_value' => (isset($record['contact_no']) && $_GET['id']) ? $record['contact_no']:'',
        );
        
        $form['status'] = array (
            '#type' => 'radios',
            '#title' => ('Status'),
            '#options' => array(
             1 =>t('Active'),
             0 =>t('Inactive')
            ),
            '#default_value' => (isset($record['status']) && $_GET['id']) ? $record['status']:'',
        );

        $form['actions']['#type'] = 'actions';

        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary',
        );

        return $form;
    }
    
    /**
    * {@inheritdoc}
    * validating the form fields
    */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $database = \Drupal::database();
        $record = array();
        $query = $database ->select('user_contact_details','ucd')
                        ->fields('ucd', ['email', 'contact_no']);
        $records = $query->execute();
        foreach($records as $record){
            $emails[] = $record->email;
            $contacts[] = $record->contact_no;
        }
        if (empty($form_state->getValue('first_name')))  {
            $form_state->setErrorByName('first_name', $this->t('Please enter your first name.'));
        }
        if (empty($form_state->getValue('last_name'))) {
            $form_state->setErrorByName('last_name', $this->t('Please enter your last name.'));
        }
        if (strlen($form_state->getValue('contact_no')) < 10) {
            $form_state->setErrorByName('contact_no', $this->t('Phone number is too short.'));
        }
        if (in_array($form_state->getValue('email'),$emails)) {
            $form_state->setErrorByName('email', $this->t('Email id already exists'));
        }
        if (in_array($form_state->getValue('contact_no'),$contacts)) {
            $form_state->setErrorByName('contact_no', $this->t('Phone no. already exists'));
        }
    }

    /**
    * {@inheritdoc}
    * Submit the form
    */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = array(
            'first_name' => $form_state->getValue('first_name'),
            'last_name' => $form_state->getValue('last_name'),
            'email' => $form_state->getValue('email'),
            'contact_no' => $form_state->getValue('contact_no'),
            'status' => $form_state->getValue('status'),
            'created_date' => date('Y-m-d H:i:s',time()),
            
        );
        $query = \Drupal::database();
        if (isset($_GET['id'])) {
            $query->update('user_contact_details')
                    ->fields($values)
                    ->condition('id', $_GET['id'])
                    ->execute();
            drupal_set_message("Contact Successfully Updated");
            $form_state->setRedirect('contact.display_contacts_list');
        }else{
            $query ->insert('user_contact_details')
                    ->fields($values)
                    ->execute();
            drupal_set_message("Contact Successfully Added");
            $form_state->setRedirect('contact.display_contacts_list');
        }
    }
    
}
