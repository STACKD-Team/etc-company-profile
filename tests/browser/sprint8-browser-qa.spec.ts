import { expect, test, type Page } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

const screenshotRoot = path.resolve('storage/app/sprint8-browser-screenshots');
const viewports = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
];

const publicPages = [
  { name: 'public-home', path: '/', marker: 'ETC Planet' },
  { name: 'public-programs', path: '/programs', heading: 'Pilih kelas yang paling pas untuk target belajarmu' },
  { name: 'public-reels', path: '/reels', marker: 'Reels' },
];

const rolePages = [
  {
    role: 'admin',
    email: 'admin@etcplanet.test',
    pages: [
      { name: 'admin-dashboard', path: '/admin/dashboard', marker: 'Dashboard Admin' },
      { name: 'admin-student', path: '/admin/student', marker: 'Data Siswa' },
      { name: 'admin-report-card', path: '/admin/report-card', marker: 'Rapor' },
    ],
  },
  {
    role: 'student',
    email: 'student@etcplanet.test',
    pages: [
      { name: 'student-dashboard', path: '/student/dashboard', heading: 'Dashboard Siswa' },
      { name: 'student-report-card', path: '/student/report-card', marker: 'Rapor' },
    ],
  },
  {
    role: 'instructor',
    email: 'sarah.amalia@etcplanet.test',
    pages: [
      { name: 'instructor-dashboard', path: '/instructor/dashboard', marker: 'Instructor Workspace' },
      { name: 'instructor-report-card', path: '/instructor/report-card', marker: 'Assessment' },
    ],
  },
];

test.beforeAll(() => {
  fs.mkdirSync(screenshotRoot, { recursive: true });
});

for (const viewport of viewports) {
  test.describe(`Sprint 8 browser QA ${viewport.name}`, () => {
    test.use({ viewport });

    for (const pageSpec of publicPages) {
      test(`${pageSpec.name} renders without horizontal overflow`, async ({ page }) => {
        await page.goto(pageSpec.path);
        await expect(pageMarker(page, pageSpec)).toBeVisible();
        await assertNoHorizontalOverflow(page);
        await page.screenshot({
          path: path.join(screenshotRoot, `${viewport.name}-${pageSpec.name}.png`),
          fullPage: true,
        });
      });
    }

    for (const roleSpec of rolePages) {
      test(`${roleSpec.role} pages render without horizontal overflow`, async ({ page }) => {
        await login(page, roleSpec.email);

        for (const pageSpec of roleSpec.pages) {
          await page.goto(pageSpec.path);
          await expect(pageMarker(page, pageSpec)).toBeVisible();
          await assertNoHorizontalOverflow(page);
          await page.screenshot({
            path: path.join(screenshotRoot, `${viewport.name}-${pageSpec.name}.png`),
            fullPage: true,
          });
        }
      });
    }
  });
}

function pageMarker(page: Page, pageSpec: { marker?: string; heading?: string }) {
  if (pageSpec.heading) {
    return page.getByRole('heading', { name: pageSpec.heading }).first();
  }

  return page.getByText(pageSpec.marker ?? '').first();
}

async function login(page: Page, email: string) {
  await page.goto('/login');
  await page.locator('input[name="email"]').fill(email);
  await page.locator('input[name="password"]').fill('password');
  await page.getByRole('button', { name: 'Masuk' }).click();
  await page.waitForLoadState('networkidle');
}

async function assertNoHorizontalOverflow(page: Page) {
  const overflow = await page.evaluate(() => {
    const doc = document.documentElement;
    return Math.ceil(doc.scrollWidth - doc.clientWidth);
  });

  expect(overflow).toBeLessThanOrEqual(2);
}
