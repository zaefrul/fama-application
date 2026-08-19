import { MockDagangNetProvider } from "./mock-dagangnet";
import { MockIFAMAProvider } from "./mock-ifama";
import type { CompanyRegistryProvider, StaffDirectoryProvider } from "@/repositories/contracts";

export function getCompanyRegistry(): CompanyRegistryProvider {
  return new MockDagangNetProvider();
}

export function getStaffDirectory(): StaffDirectoryProvider {
  return new MockIFAMAProvider();
}
