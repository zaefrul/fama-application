import { PrismaClient } from "@prisma/client";
import bcrypt from "bcryptjs";
import {
  applications,
  approvals,
  auditLogs,
  certificates,
  companies,
  companyProduce,
  galleryItems,
  notifications,
  produceTypes,
  qrCodes,
  users,
} from "../src/fixtures/data";

const prisma = new PrismaClient();

async function main() {
  await prisma.notification.deleteMany();
  await prisma.auditLog.deleteMany();
  await prisma.approval.deleteMany();
  await prisma.qrCode.deleteMany();
  await prisma.exportApplication.deleteMany();
  await prisma.galleryItem.deleteMany();
  await prisma.certificate.deleteMany();
  await prisma.companyProduce.deleteMany();
  await prisma.user.deleteMany();
  await prisma.produceType.deleteMany();
  await prisma.company.deleteMany();

  for (const produce of produceTypes) {
    await prisma.produceType.create({ data: produce });
  }
  for (const company of companies) {
    await prisma.company.create({
      data: {
        ...company,
        createdAt: new Date(company.createdAt),
      },
    });
  }
  for (const user of users) {
    await prisma.user.create({
      data: {
        id: user.id,
        name: user.name,
        email: user.email,
        passwordHash: await bcrypt.hash(user.password, 10),
        role: user.role,
        identityReference: user.identityReference,
        status: user.status,
        companyId: user.companyId,
        createdAt: new Date(user.createdAt),
      },
    });
  }
  for (const row of companyProduce) {
    await prisma.companyProduce.create({ data: row });
  }
  for (const certificate of certificates) {
    await prisma.certificate.create({
      data: {
        ...certificate,
        issueDate: new Date(certificate.issueDate),
        expiryDate: certificate.expiryDate ? new Date(certificate.expiryDate) : null,
      },
    });
  }
  for (const item of galleryItems) {
    await prisma.galleryItem.create({
      data: { ...item, uploadedAt: new Date(item.uploadedAt) },
    });
  }
  for (const application of applications) {
    await prisma.exportApplication.create({
      data: {
        ...application,
        exportDate: new Date(application.exportDate),
        submittedAt: application.submittedAt ? new Date(application.submittedAt) : null,
        reviewedAt: application.reviewedAt ? new Date(application.reviewedAt) : null,
        createdAt: new Date(application.createdAt),
        updatedAt: new Date(application.updatedAt),
      },
    });
  }
  for (const qr of qrCodes) {
    await prisma.qrCode.create({
      data: {
        ...qr,
        generatedAt: new Date(qr.generatedAt),
        activatedAt: qr.activatedAt ? new Date(qr.activatedAt) : null,
      },
    });
  }
  for (const approval of approvals) {
    await prisma.approval.create({
      data: { ...approval, decidedAt: new Date(approval.decidedAt) },
    });
  }
  for (const log of auditLogs) {
    await prisma.auditLog.create({
      data: { ...log, createdAt: new Date(log.createdAt) },
    });
  }
  for (const notification of notifications) {
    await prisma.notification.create({
      data: { ...notification, createdAt: new Date(notification.createdAt) },
    });
  }
}

main()
  .then(() => prisma.$disconnect())
  .catch(async (error) => {
    console.error(error);
    await prisma.$disconnect();
    process.exit(1);
  });
