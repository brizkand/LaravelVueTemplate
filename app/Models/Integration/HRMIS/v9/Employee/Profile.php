<?php

namespace App\Models\Integration\HRMIS\v9\Employee;

use App\Models\Integration\HRMIS\v9\Employee\Account as HRMISAccount;
use App\Models\Integration\HRMIS\v9\Employee\EmployeePosition as HRMISEmployeePosition;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  protected $connection = 'hrmis';
  protected $table = 'tblEmpPersonal';
  protected $primaryKey = 'empID';
  public $timestamps = false;

  protected $fillable = [
    'empID',
    'empNumber',
    'surname',
    'firstname',
    'middlename',
    'middleInitial',
    'nameExtension',
    'salutation',
    'sex',
    'civilStatus',
    'spouse',
    'spouseSurname',
    'spouseFirstname',
    'spouseMiddlename',
    'spousenameExtension',
    'spouseWork',
    'spouseBusName',
    'spouseBusAddress',
    'spouseTelephone',
    'tin',
    'citizenship',
    'dualCitizenshipType',
    'dualCitizenshipCountryId',
    'birthday',
    'birthPlace',
    'bloodType',
    'height',
    'weight',
    'residentialAddress',
    'lot1',
    'street1',
    'subdivision1',
    'barangay1',
    'city1',
    'province1',
    'zipCode1',
    'telephone1',
    'permanentAddress',
    'lot2',
    'street2',
    'subdivision2',
    'barangay2',
    'city2',
    'province2',
    'zipCode2',
    'telephone2',
    'mobile',
    'email',
    'fatherName',
    'fatherSurname',
    'fatherFirstname',
    'fatherMiddlename',
    'fathernameExtension',
    'motherName',
    'motherSurname',
    'motherFirstname',
    'motherMiddlename',
    'parentAddress',
    'skills',
    'nadr',
    'miao',
    'relatedThird',
    'relatedDegreeParticularsThird',
    'relatedFourth',
    'relatedDegreeParticulars',
    'violateLaw',
    'violateLawParticulars',
    'formallyCharged',
    'formallyChargedParticulars',
    'adminCase',
    'adminCaseParticulars',
    'forcedResign',
    'forcedResignParticulars',
    'candidate',
    'candidateParticulars',
    'campaign',
    'campaignParticulars',
    'immigrant',
    'immigrantParticulars',
    'indigenous',
    'indigenousParticulars',
    'disabled',
    'disabledParticulars',
    'soloParent',
    'soloParentParticulars',
    'signature',
    'dateAccomplished',
    'comTaxNumber',
    'issuedAt',
    'issuedOn',
    'gsisNumber',
    'businessPartnerNumber',
    'philHealthNumber',
    'sssNumber',
    'pagibigNumber',
    'AccountNum'
  ];

  public function account () {
    return $this->hasOne(
      HRMISAccount::class,
      'empNumber',
      'empNumber'
    );
  }

  public function position () {
    return $this->hasOne(
      HRMISEmployeePosition::class,
      'empNumber',
      'empNumber'
    );
  }
}
