<?php /** @var array $site */
$storiesData = require __DIR__ . '/../data/success_stories.php';
$section = $storiesData['section'];
$storiesTableExists = App\Database::fetchValue(
  'SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?',
  ['success_stories']
);
$storyRows = $storiesTableExists
  ? App\Database::fetchAll(
    'SELECT m.path AS photo_path
     FROM success_stories s
     LEFT JOIN media m ON m.id=s.photo_id
     WHERE s.active=1 AND m.path IS NOT NULL AND m.path <> \'\'
     ORDER BY s.sort, s.id'
  )
  : [];
if (!$storiesTableExists) {
  error_log('Student success stories are disabled until config/migration.sql creates the success_stories table.');
}
$stories = array_map(static fn(array $story): string => App\Upload::url($story['photo_path']), $storyRows);
$carousel = count($stories) > 4;
$section['headline'] = App\Homepage::text('success.headline', $section['headline']);
?>
<?php if ($stories): ?>
<section id="student-success-stories" class="student-success-section">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="student-success-heading">
      <div>
        <span class="student-success-eyebrow"><span></span><?= h(t('testimonials.eyebrow')) ?></span>
        <h2 class="font-display"><?= h($section['headline']) ?></h2>
      </div>
      <span class="student-success-count"><?= count($stories) ?> <?= count($stories) === 1 ? 'moment' : 'moments' ?></span>
    </div>

    <?php if ($carousel): ?>
      <div class="student-success-carousel">
        <div class="student-success-track">
          <?php foreach ([false, true] as $duplicate): ?>
            <div class="student-success-set" <?= $duplicate ? 'aria-hidden="true"' : '' ?>>
              <?php foreach ($stories as $photo): ?>
                <div class="student-success-card success-story-card group">
                  <img src="<?= h($photo) ?>" alt="Student success story" class="success-story-photo" loading="lazy" <?= $duplicate ? 'aria-hidden="true"' : '' ?>>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        <?php foreach ($stories as $photo): ?>
          <div class="student-success-card success-story-card group">
            <img src="<?= h($photo) ?>" alt="Student success story" class="success-story-photo" loading="lazy">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
<style>
.student-success-section {
  overflow: hidden;
  padding: 3.5rem 0;
  background:
    radial-gradient(ellipse at 82% 10%, rgba(8,118,66,0.055), transparent 28rem),
    #fff;
}
.student-success-heading {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.4rem;
}
.student-success-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #77786f;
  font-size: 0.62rem;
  font-weight: 800;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}
.student-success-eyebrow > span {
  width: 1.25rem;
  height: 1px;
  background: #087642;
}
.student-success-heading h2 {
  margin-top: 0.5rem;
  color: #1e242b;
  font-size: clamp(1.7rem, 3vw, 2.6rem);
  font-weight: 700;
  line-height: 1.12;
  letter-spacing: -0.025em;
}
.student-success-count {
  flex: 0 0 auto;
  padding: 0.45rem 0.7rem;
  color: #315547;
  border: 1px solid #dce8df;
  border-radius: 999px;
  background: #f3f7f3;
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}
.student-success-carousel {
  container-type: inline-size;
  overflow: hidden;
  padding: 0.15rem 0.1rem 0.65rem;
}
.student-success-track {
  display: flex;
  width: max-content;
  animation: studentSuccessScroll 36s linear infinite;
}
.student-success-carousel:hover .student-success-track {
  animation-play-state: paused;
}
.student-success-set {
  display: flex;
  gap: 1rem;
  padding-right: 1rem;
}
.student-success-card {
  flex: 0 0 calc((100cqi - 1rem) / 2);
  overflow: hidden;
  aspect-ratio: 0.84 / 1;
  border: 1px solid rgba(215,210,199,0.8);
  border-radius: 0.9rem;
  background: #e8e6e0;
  box-shadow: 0 12px 30px -23px rgba(30,36,43,0.42);
}
@media (min-width: 768px) {
  .student-success-card {
    flex-basis: calc((100cqi - 2.25rem) / 3);
  }
}
@media (min-width: 1024px) {
  .student-success-section {
    padding: 4rem 0;
  }
  .student-success-set {
    gap: 1.25rem;
    padding-right: 1.25rem;
  }
  .student-success-card {
    flex-basis: calc((100cqi - 3.75rem) / 4);
  }
}
.student-success-photo,
.success-story-photo {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  transition: transform 650ms cubic-bezier(0.2,0.7,0.2,1);
}
.student-success-card:hover .success-story-photo {
  transform: scale(1.045);
}
@media (max-width: 767px) {
  .student-success-section {
    padding: 2.5rem 0;
  }
  .student-success-heading {
    margin-bottom: 1rem;
  }
  .student-success-heading h2 {
    font-size: 1.55rem;
  }
  .student-success-count {
    padding: 0.35rem 0.55rem;
    font-size: 0.55rem;
  }
}
@keyframes studentSuccessScroll {
  to { transform: translateX(-50%); }
}
@media (prefers-reduced-motion: reduce) {
  .student-success-track {
    display: block;
    width: 100%;
    animation: none;
  }
  .student-success-set:first-child {
    display: grid;
    width: 100%;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    padding: 0;
  }
  .student-success-set:last-child {
    display: none;
  }
  .student-success-card {
    width: auto;
    flex-basis: auto;
  }
}
@media (prefers-reduced-motion: reduce) and (min-width: 768px) {
  .student-success-set:first-child {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
@media (prefers-reduced-motion: reduce) and (min-width: 1024px) {
  .student-success-set:first-child {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1.25rem;
  }
}
</style>
