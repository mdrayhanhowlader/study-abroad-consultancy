<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';

$site = App\DataAdapter::site();
$batches = App\ClassBatch::all(true);
$hasDemoBatches = count(array_filter(
  $batches,
  static fn(array $item): bool => str_starts_with((string) $item['batch_code'], 'DEMO-DELETE-')
)) > 0;
$requestedCode = trim((string) ($_GET['batch'] ?? ''));
$batch = $requestedCode !== '' ? App\ClassBatch::findActive($requestedCode) : null;
$batch ??= $batches[0] ?? null;
$calendar = $batch ? App\ClassBatch::calendar($batch) : null;
$page_title = 'Class Schedule & Holidays — ' . $site['brand']['full_name'];
$page_desc = 'View class days and government holidays for each active batch.';
$canonical = $site['brand']['domain'] . '/batch-schedule.php';
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { paper:'#FAF8F4','paper-2':'#F4F1EA',ink:{DEFAULT:'#1E242B',2:'#4A5159',3:'#8A857C'},hair:{DEFAULT:'#E5E1D9',2:'#D7D2C7'},brand:{DEFAULT:'#006837',dark:'#004d28',light:'#cce8d6'},navy:{DEFAULT:'#1A2B6D',dark:'#101f4f',light:'#d6dcec'},accent:{DEFAULT:'#E31E24',dark:'#a8131a',light:'#f7c8ca'},gold:'#B8923B' },
      fontFamily: { display:['"Playfair Display"','Georgia','serif'], sans:['Inter','ui-sans-serif','system-ui','sans-serif'] }
    }}}
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= h(asset('css/style.css')) ?>?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <?php require __DIR__ . '/partials/favicon.php'; ?>
</head>
<body class="font-sans text-ink antialiased bg-paper schedule-page">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main>
    <section class="schedule-hero">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <p class="schedule-eyebrow"><span aria-hidden="true"></span>Student resources</p>
        <h1 class="font-display">Class schedule <span>&amp; holidays</span></h1>
        <p>Choose your batch to see the complete course period, weekly class days, and scheduled holidays.</p>
      </div>
    </section>

    <section class="schedule-content mx-auto max-w-7xl px-4 lg:px-6">
      <?php if ($hasDemoBatches): ?>
        <div class="schedule-demo-warning">
          <span class="schedule-warning-icon" aria-hidden="true">i</span>
          <p><strong>Sample schedules for preview.</strong> DEMO-DELETE batches and “SAMPLE HOLIDAY — DELETE” are examples only. Remove them from Dashboard → Class Batches &amp; Holidays before publishing.</p>
        </div>
      <?php endif; ?>
      <?php if (!$batches): ?>
        <div class="schedule-empty">
          <h2 class="font-display text-2xl font-bold">No schedules published yet</h2>
          <p class="mt-2 text-sm text-gray-600">Please contact our team for current class schedules.</p>
        </div>
      <?php else: ?>
        <div class="schedule-layout">
          <aside class="schedule-sidebar">
            <form method="get" class="schedule-select-card">
              <label for="batch"><span class="schedule-step">01</span>Select your batch</label>
              <select id="batch" name="batch" onchange="this.form.submit()">
                <?php foreach ($batches as $option): ?>
                  <option value="<?= h($option['batch_code']) ?>" <?= $batch && $batch['batch_code'] === $option['batch_code'] ? 'selected' : '' ?>><?= h($option['batch_code'] . ' — ' . $option['course_name']) ?></option>
                <?php endforeach; ?>
              </select>
              <noscript><button class="schedule-submit">View schedule</button></noscript>
            </form>

            <?php if ($batch && $calendar): ?>
              <div class="schedule-batch-card">
                <div class="schedule-batch-topline">
                  <p><?= h($batch['batch_code']) ?></p>
                  <?php if (str_starts_with((string) $batch['batch_code'], 'DEMO-DELETE-')): ?>
                    <span class="schedule-sample-pill">Sample</span>
                  <?php endif; ?>
                </div>
                <h2><?= h($batch['course_name']) ?></h2>
                <dl class="schedule-facts">
                  <div><dt>Course period</dt><dd><?= h($calendar['start']->format('d M Y')) ?> – <?= h($calendar['end']->format('d M Y')) ?></dd></div>
                  <div><dt>Duration</dt><dd><?= (int) $batch['duration_months'] ?> months</dd></div>
                  <div><dt>Class days</dt><dd><?= h(implode(', ', $calendar['weekdays'])) ?></dd></div>
                  <?php if ($batch['class_time']): ?><div><dt>Class time</dt><dd><?= h($batch['class_time']) ?></dd></div><?php endif; ?>
                  <?php if ($batch['class_location']): ?><div><dt>Location / format</dt><dd><?= h($batch['class_location']) ?></dd></div><?php endif; ?>
                  <div class="schedule-fact-total"><dt>Scheduled class days</dt><dd><?= (int) $calendar['class_days'] ?> days <span>excluding listed holidays</span></dd></div>
                </dl>
              </div>
            <?php endif; ?>
          </aside>

          <?php if ($batch && $calendar): ?>
            <div class="schedule-calendar-column">
              <div class="schedule-legend">
                <span><i class="schedule-legend-class"></i>Class day</span>
                <span><i class="schedule-legend-holiday"></i>Friday / government holiday</span>
                <span><i class="schedule-legend-other"></i>Other date</span>
              </div>
              <div class="schedule-month-grid">
                <?php foreach ($calendar['months'] as $month): ?>
                  <section class="schedule-month">
                    <h2><?= h($month['label']) ?></h2>
                    <div class="schedule-weekdays">
                      <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $index => $weekday): ?><div class="<?= $index === 4 ? 'is-friday' : '' ?>"><?= $weekday ?></div><?php endforeach; ?>
                    </div>
                    <div class="schedule-days">
                      <?php foreach ($month['days'] as $day): ?>
                        <?php if ($day === null): ?>
                          <div class="schedule-day schedule-day--empty"></div>
                        <?php else: ?>
                          <?php $dayClass = 'schedule-day' . (!$day['in_range'] ? ' is-outside' : '') . ($day['holiday'] ? ' is-holiday' : ($day['friday'] ? ' is-friday' : ($day['class'] ? ' is-class-day' : ''))); ?>
                          <div class="<?= $dayClass ?>" <?= $day['holiday'] ? 'title="' . h($day['holiday']) . '"' : '' ?>>
                            <span class="schedule-day-number"><?= (int) $day['day'] ?></span>
                            <?php if ($day['holiday']): ?><span class="schedule-holiday-name"><?= h($day['holiday']) ?></span><?php endif; ?>
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  </section>
                <?php endforeach; ?>
              </div>

              <section class="schedule-holidays">
                <div class="schedule-holidays-title">
                  <span class="schedule-holidays-icon" aria-hidden="true">✦</span>
                  <h2>Government holidays during this course</h2>
                </div>
                <?php if (!$calendar['holidays']): ?>
                  <p class="schedule-no-holidays">No government holidays are currently listed for this batch period.</p>
                <?php else: ?>
                  <ul class="schedule-holiday-list">
                    <?php foreach ($calendar['holidays'] as $holiday): ?>
                      <li><time><?= h(date('d M Y', strtotime($holiday['holiday_date']))) ?></time><span><?= h($holiday['title']) ?></span></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                <p class="schedule-holiday-note">Schedules may change if new government holidays are announced. Contact the office if your batch schedule needs confirmation.</p>
              </section>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
