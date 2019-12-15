<?php
namespace Drupal\contact\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
/**
 * Class DisplayContactsController.
 *
 * @package Drupal\contact\Controller
 */
class DisplayContactsController extends ControllerBase {
    public function display() {
        //create table headers first
        $header_table = array(
            'id'=> t('Id'),
            'first_name' => t('First Name'),
            'last_name' => t('Last Name'),
            'status' => t('Status'),
            'opt' => t('Operation'),
            'opt1' => t('Operation'),
        );
        //select records from table
        $database = \Drupal::database();
        $query = $database->select('user_contact_details', 'ucd')
                            ->fields('ucd', ['id','first_name','last_name','status']);
                            
        $results = $query->execute()->fetchAll();
        $rows = array();
        foreach($results as $data){
            $delete = Url::fromUserInput('/contact/form/delete/'.$data->id);
            $edit = Url::fromUserInput('/contact/form?id='.$data->id);
            //print the data from table
            $rows[] = array(
                'id' =>$data->id,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'status' => (($data->status==1?"Active":"Inactive")),
                //Email and Contact no not displayed on list for data privacy
                // 'email' => $data->email,
                // 'contact_no' => $data->contact_no,
                \Drupal::l('Delete', $delete),
                \Drupal::l('Edit', $edit),
            );
        }

        //display data in site
        $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No users found'),
        ];
        return $form;
    }
}
