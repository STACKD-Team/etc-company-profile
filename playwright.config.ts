import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/browser',
  timeout: 60_000,
  expect: {
    timeout: 10_000,
  },
  use: {
    baseURL: 'http://127.0.0.1:8008',
    channel: 'chrome',
    trace: 'retain-on-failure',
  },
  webServer: {
    command:
      'powershell -NoProfile -Command "if (Test-Path public/hot) { Remove-Item public/hot -Force }; if (!(Test-Path database/playwright.sqlite)) { New-Item -ItemType File -Path database/playwright.sqlite -Force | Out-Null }; php artisan migrate:fresh --seed --env=playwright; php artisan serve --env=playwright --host=127.0.0.1 --port=8008"',
    url: 'http://127.0.0.1:8008',
    reuseExistingServer: false,
    timeout: 120_000,
  },
});
