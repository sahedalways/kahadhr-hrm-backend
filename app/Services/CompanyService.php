<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use App\Models\Company;

class CompanyService
{
  protected $companyRepo;

  public function __construct(CompanyRepository $companyRepo)
  {
    $this->companyRepo = $companyRepo;
  }

  /**
   * Create a new company
   *
   * @param array $data
   * @return Company
   */
  public function createCompany(array $data): Company
  {
    return $this->companyRepo->create($data);
  }

  /**
   * Update company
   *
   * @param Company $company
   * @param array $data
   * @return Company
   */
  public function updateCompany(Company $company, array $data): Company
  {
    return $this->companyRepo->update($company, $data);
  }

  /**
   * Get company by ID
   *
   * @param int $id
   * @return Company|null
   */
  public function getCompany($id): ?Company
  {
    return $this->companyRepo->find($id);
  }

  /**
   * Get all companies
   *
   * @return \Illuminate\Support\Collection
   */
  public function getAllCompanies()
  {
    return $this->companyRepo->getAll();
  }

  /**
   * Delete company
   *
   * @param int $id
   * @return bool
   */
  public function deleteCompany($id): bool
  {
    $company = $this->getCompany($id);

    if (!$company) {
      return false;
    }

    return $this->companyRepo->delete($company);
  }
}
