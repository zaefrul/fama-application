# Visual QA Procedure

A UI task is not complete merely because the route renders.

## Required workflow

```text
Implement
→ run app
→ open target route
→ use required viewport
→ capture screenshot
→ compare with reference
→ fix visible discrepancies
→ document intentional differences
```

## Baseline viewports

```text
Mobile: 390 × 844
Desktop: 1440 × 900
```

## Comparison checklist

### Structure

- [ ] Correct header/navigation
- [ ] Correct main sections
- [ ] Correct ordering
- [ ] Correct card/table/list pattern

### Spacing

- [ ] Page padding consistent
- [ ] Card gaps consistent
- [ ] Form spacing consistent
- [ ] Buttons aligned correctly

### Typography

- [ ] Page title hierarchy
- [ ] Section titles
- [ ] Body/helper hierarchy
- [ ] KPI values visually prominent

### Status

- [ ] Correct status label
- [ ] Correct semantic color
- [ ] Same StatusBadge component used

### Forms

- [ ] Read-only vs editable fields clear
- [ ] Required labels visible
- [ ] Validation state readable
- [ ] Primary action clear

### QR

- [ ] QR is sharp
- [ ] Adequate quiet space
- [ ] Correct identifier/state
- [ ] Active/inactive state matches data

### Responsive

- [ ] No horizontal overflow
- [ ] Main actions remain usable
- [ ] Cards adapt logically
- [ ] Navigation remains accessible

## Completion report template

```text
Screen:
Route:
Reference:
Viewport:

Visual QA:
- Structure: PASS/FAIL
- Spacing: PASS/FAIL
- Typography: PASS/FAIL
- Status: PASS/FAIL
- Responsive: PASS/FAIL

Intentional differences:
1.
2.

Screenshot:
<path>
```
