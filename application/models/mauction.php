<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename mauction.php
* @touch date Wednesday, May 15, 2013 PM12:21:50 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MAuction extends CI_Model {

/*{{{ insert */
    public function insert($param) {
        $this->db->set("created", "now()", false);
        $this->db->set("updated", "now()", false);
        if ($this->db->insert("##auction", $param)) {
            return $this->db->insert_id();
        }

        return false;
    }
/*}}}*/
/*{{{ stat */
    public function stat($param) {

        return false;
    }
/*}}}*/


}
