import { companies, dagangNetDirectory } from "@/fixtures/data";
import type { CompanyRegistryProvider, CompanyRegistryResult } from "@/repositories/contracts";

export class MockDagangNetProvider implements CompanyRegistryProvider {
  async findCompany(identifier: string): Promise<CompanyRegistryResult | null> {
    const match = dagangNetDirectory.find(
      (row) => row.identifier.toLowerCase() === identifier.trim().toLowerCase(),
    );
    if (!match) return null;
    const company = companies.find((row) => row.id === match.companyId);
    if (!company) return null;
    return {
      identifier: match.identifier,
      registrationNo: company.registrationNo,
      name: company.name,
      email: company.email,
      status: company.externalStatus,
      address: company.address,
      state: company.state,
      district: company.district,
      postcode: company.postcode,
      phone: company.phone,
    };
  }
}
