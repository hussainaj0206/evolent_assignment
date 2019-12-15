<?php
namespace Drupal\contact\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
/**
 * Class DeleteForm.
 *
 * @package Drupal\contact\Form
 */
class DeleteForm extends ConfirmFormBase {
    public $id;
    
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'delete_form';
    }
 
    public function getQuestion() { 
        return t('Do you want to delete this contact?', array('%id' => $this->id));
    }

    public function getCancelUrl() {
        return new Url('contact.display_contacts_list');
    }
    
    public function getDescription() {
        return t('Only do this if you are sure!');
    }
    /**
     * {@inheritdoc}
     */
    public function getConfirmText() {
        return t('Delete');
    }
    /**
     * {@inheritdoc}
     */
    public function getCancelText() {
        return t('Cancel');
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        $this->id = $id;
        return parent::buildForm($form, $form_state);
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $query = \Drupal::database();
        $query->delete('user_contact_details')
                ->condition('id',$this->id)
                ->execute();

        drupal_set_message('Contact Successfully deleted');
        $form_state->setRedirect('contact.display_contacts_list');
    }
}
