import NextAuth from "next-auth";
import Credentials from "next-auth/providers/credentials";
import bcrypt from "bcryptjs";
import { getRepositories } from "@/repositories";
import type { Role } from "@/domain/types";

export const { handlers, auth, signIn, signOut } = NextAuth({
  trustHost: true,
  providers: [
    Credentials({
      credentials: {
        email: {},
        password: {},
      },
      authorize: async (credentials) => {
        const email = String(credentials.email ?? "");
        const password = String(credentials.password ?? "");
        const user = await getRepositories().findUserByEmail(email);
        if (!user) return null;
        const valid = user.password.startsWith("$2")
          ? await bcrypt.compare(password, user.password)
          : user.password === password;
        if (!valid) return null;
        return {
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role,
          companyId: user.companyId,
        };
      },
    }),
  ],
  callbacks: {
    jwt({ token, user }) {
      if (user) {
        token.sub = user.id;
        token.role = (user as { role: Role }).role;
        token.companyId = (user as { companyId: string | null }).companyId;
      }
      return token;
    },
    session({ session, token }) {
      session.user.id = token.sub ?? "";
      session.user.role = token.role as Role;
      session.user.companyId = (token.companyId as string | null) ?? null;
      return session;
    },
  },
});
