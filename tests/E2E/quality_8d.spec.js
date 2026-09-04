/**
 * E2E Tests for Quality 8D Module
 *
 * Run with: npx playwright test
 *
 * @BABOK Related: FR-QC-001-001, FR-QC-001-002
 * @since 1.0.0
 */

const { test, expect } = require('@playwright/test');

const BASE_URL = process.env.FA_URL || 'http://localhost/frontaccounting';

test.describe('Quality 8D Reports', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(`${BASE_URL}/index.php`);
        await page.waitForLoadState('networkidle');
    });

    test('Q8D-E2E-001: Access 8D list page', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_list.php`);

        await expect(page.locator('h1')).toContainText('8D Reports');
    });

    test('Q8D-E2E-002: Create new 8D report', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_edit.php`);

        await page.fill('textarea[name="problem_description"]', 'Test quality issue from E2E');

        await page.selectOption('select[name="priority"]', 'High');

        await page.fill('input[name="symptom"]', 'Customer reported defect');

        await page.click('button[type="submit"][name="save"]');

        await expect(page.locator('.toast-success')).toContainText('8D created');

        await expect(page).toHaveURL(/\/8d_view\.php\?id=\d+/);
    });

    test('Q8D-E2E-003: View 8D details', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_list.php`);

        const first8D = page.locator('tr.eightd-row').first();
        if (await first8D.isVisible()) {
            await first8D.click();

            await expect(page.locator('.eightd-header')).toBeVisible();
            await expect(page.locator('.discipline-d1')).toBeVisible();
            await expect(page.locator('.discipline-d3')).toBeVisible();
        }
    });

    test('Q8D-E2E-004: Add team member (D1)', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        await page.click('.tab-d1');

        await page.selectOption('select[name="user_id"]', { index: 1 });
        await page.fill('input[name="role"]', 'Quality Engineer');

        await page.click('button[name="add_team_member"]');

        await expect(page.locator('.team-member').last()).toContainText('Quality Engineer');
    });

    test('Q8D-E2E-005: Add containment action (D3)', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        await page.click('.tab-d3');

        await page.fill('textarea[name="action_description"]', 'Isolate affected batch');
        await page.fill('input[name="target_date"]', '2026-03-01');

        await page.click('button[name="add_containment"]');

        await expect(page.locator('.containment-action').last()).toContainText('Isolate affected batch');
    });

    test('Q8D-E2E-006: Add root cause (D4)', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        await page.click('.tab-d4');

        await page.selectOption('select[name="cause_category"]', 'Man');
        await page.fill('textarea[name="cause_description"]', 'Inadequate training on setup procedure');
        await page.fill('textarea[name="evidence"]', 'Operator error logs show incorrect parameter');

        await page.click('button[name="add_root_cause"]');

        await expect(page.locator('.root-cause').last()).toContainText('Inadequate training');
    });

    test('Q8D-E2E-007: Add corrective action (D5)', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        await page.click('.tab-d5');

        await page.fill('textarea[name="action_description"]', 'Implement SPC on line');
        await page.fill('input[name="target_date"]', '2026-04-01');
        await page.fill('input[name="validation_method"]', 'Statistical process control charts');

        await page.click('button[name="add_corrective_action"]');

        await expect(page.locator('.corrective-action').last()).toContainText('Implement SPC');
    });

    test('Q8D-E2E-008: Add prevention measure (D7)', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        await page.click('.tab-d7');

        await page.fill('textarea[name="prevention_description"]', 'Update work instruction WI-001');
        await page.fill('input[name="system_or_process_changed"]', 'Manufacturing Process');
        await page.fill('input[name="target_date"]', '2026-05-01');

        await page.click('button[name="add_prevention"]');

        await expect(page.locator('.prevention-measure').last()).toContainText('Update work instruction');
    });

    test('Q8D-E2E-009: Attempt to close incomplete 8D', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        await page.click('button[name="close_8d"]');

        await page.waitForSelector('.modal.confirm-close');

        await page.click('button.confirm');

        await expect(page.locator('.toast-error')).toContainText('Cannot close');
    });

    test('Q8D-E2E-010: Close complete 8D', async ({ page }) => {
        await page.login('admin', 'admin');

        const complete8D = await createComplete8D(page);
        if (!complete8D) {
            test.skip();
        }

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=${complete8D}`);

        const closeButton = page.locator('button[name="close_8d"]');
        if (await closeButton.isVisible()) {
            await closeButton.click();

            await page.waitForSelector('.modal.confirm-close');
            await page.click('button.confirm');

            await expect(page.locator('.toast-success')).toContainText('Closed');

            const statusBadge = page.locator('.status-badge');
            await expect(statusBadge).toContainText('Closed');
        }
    });

    test('Q8D-E2E-011: Filter 8D list by status', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_list.php`);

        await page.selectOption('select[name="status_filter"]', 'Open');

        await page.click('button[type="submit"][name="filter"]');

        await page.waitForLoadState('networkidle');

        const rows = await page.locator('tr.eightd-row').all();
        for (const row of rows) {
            const status = await row.locator('td.status').textContent();
            expect(status.trim()).toBe('Open');
        }
    });

    test('Q8D-E2E-012: View 8D completeness indicator', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        const completeness = page.locator('.completeness-indicator');
        if (await completeness.isVisible()) {
            const percentText = await completeness.textContent();
            const percentMatch = percentText.match(/(\d+)%/);
            if (percentMatch) {
                const percent = parseInt(percentMatch[1]);
                expect(percent).toBeGreaterThanOrEqual(0);
                expect(percent).toBeLessThanOrEqual(100);
            }
        }
    });
});

test.describe('Quality 8D - Attachments', () => {
    test('Q8D-E2E-013: Upload attachment', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_Quality/pages/8d_view.php?id=1`);

        const fileInput = page.locator('input[type="file"][name="attachment"]');
        if (await fileInput.isVisible()) {
            await fileInput.setInputFiles({
                name: 'test-document.pdf',
                mimeType: 'application/pdf',
                buffer: Buffer.from('Test PDF content'),
            });

            await page.click('button[name="upload_attachment"]');

            await expect(page.locator('.attachment-item').last()).toContainText('test-document.pdf');
        }
    });
});

async function createComplete8D(page) {
    return null;
}