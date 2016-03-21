<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Model\Fixtures\AuditFixtures;

/**
 * Description of Audit
 *
 * @author dhayward
 */
class Audit extends BaseModel {
    public function testDataForAudit($id){
        
        $data = new AuditFixtures();
        return $data->auditFixtures($id);
        
    }
}
