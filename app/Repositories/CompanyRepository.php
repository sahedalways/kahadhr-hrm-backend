<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
  /**
   * Create a new company
   *
   * @param array $data
   * @return Company
   */
  public function create(array $data): Company
  {
    $company = new Company();
    $company->user_id          = $data['user_id'];
    $company->company_name     = $data['company_name'];
    $company->company_house_number = $data['company_house_number'] ?? null;
    $company->company_mobile   = $data['company_mobile'] ?? null;
    $company->company_email    = $data['company_email'] ?? null;
    $company->business_type    = $data['business_type'] ?? null;
    $company->company_address  = $data['company_address'] ?? null;
    $company->company_logo     = $data['company_logo'] ?? null;
    $company->save();

    return $company;
  }

  /**
   * Update an existing company
   *
   * @param Company $company
   * @param array $data
   * @return Company
   */
  public function update(Company $company, array $data): Company
  {
    $company->company_name     = $data['company_name'] ?? $company->company_name;
    $company->company_house_number = $data['company_house_number'] ?? $company->company_house_number;
    $company->company_mobile   = $data['company_mobile'] ?? $company->company_mobile;
    $company->company_email    = $data['company_email'] ?? $company->company_email;
    $company->business_type    = $data['business_type'] ?? $company->business_type;
    $company->company_address  = $data['company_address'] ?? $company->company_address;
    $company->company_logo     = $data['company_logo'] ?? $company->company_logo;

    $company->save();

    return $company;
  }

  /**
   * Find a company by ID
   *
   * @param int $id
   * @return Company|null
   */
  public function find($id): ?Company
  {
    return Company::find($id);
  }

  /**
   * Delete a company
   *
   * @param Company $company
   * @return bool
   */
  public function delete(Company $company): bool
  {
    return $company->delete();
  }

  /**
   * Get all companies
   *
   * @return \Illuminate\Support\Collection
   */
  public function getAll()
  {
    return Company::latest()->get();
  }
}
