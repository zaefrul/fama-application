import { ifamaDirectory } from "@/fixtures/data";
import type { StaffDirectoryProvider, StaffDirectoryResult } from "@/repositories/contracts";

export class MockIFAMAProvider implements StaffDirectoryProvider {
  async findStaff(identifier: string): Promise<StaffDirectoryResult | null> {
    const match = ifamaDirectory.find((row) => row.identifier === identifier.trim());
    if (!match) return null;
    return {
      identifier: match.identifier,
      fullName: match.fullName,
      email: match.email,
      position: match.position,
      active: match.active,
    };
  }
}
