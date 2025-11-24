<?php

namespace App\Repositories;

use App\Models\Company;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

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
    $company->company_name           = $data['company_name'] ?? $company->company_name;
    $company->company_house_number   = $data['company_house_number'] ?? $company->company_house_number;
    $company->company_mobile         = $data['company_mobile'] ?? $company->company_mobile;
    $company->company_email          = $data['company_email'] ?? $company->company_email;
    $company->business_type          = $data['business_type'] ?? $company->business_type;
    $company->address_contact_info   = $data['address_contact_info'] ?? $company->address_contact_info;
    $company->registered_domain      = $data['registered_domain'] ?? $company->registered_domain;
    $company->calendar_year          = $data['calendar_year'] ?? $company->calendar_year;
    $company->subscription_status    = $data['subscription_status'] ?? $company->subscription_status;
    $company->subscription_start     = $data['subscription_start'] ?? $company->subscription_start;
    $company->subscription_end       = $data['subscription_end'] ?? $company->subscription_end;

    // Handle company logo like your ResortImage example
    if (isset($data['company_logo']) && $data['company_logo'] instanceof UploadedFile) {
      $image = $data['company_logo'];


      if ($company->company_logo && file_exists(storage_path('app/public/' . $company->company_logo))) {
        unlink(storage_path('app/public/' . $company->company_logo));
      }

      $directory = storage_path('app/public/image/company/logo');

      if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
      }


      $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

      $img = Image::read($image->getRealPath());


      $path = $directory . '/' . $filename;

      $img->save($path);

      $company->company_logo = 'image/company/logo/' . $filename;
    }



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
