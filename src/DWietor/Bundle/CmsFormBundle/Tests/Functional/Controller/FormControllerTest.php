<?php

namespace DWietor\Bundle\CmsFormBundle\Tests\Functional\Controller;

use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use DWietor\Bundle\CmsFormBundle\Entity\CmsFormField;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormControllerTest extends WebTestCase
{
    protected const GRID = 'dwietor-cms-forms-grid';
    protected const FIELDS_GRID = 'dwietor-cms-form-fields-grid';
    protected const RESPONSES_GRID = 'dwietor-cms-form-responses-grid';

    protected function setUp()
    {
        $this->initClient([], static::generateBasicAuthHeader());
        $this->client->useHashNavigation(true);
        $this->loadFixtures(['@DWietorCmsFormBundle/Tests/Functional/DataFixtures/cms_forms.yml']);
    }

    public function testIndex()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->getUrl('d_wietor_cms_form_index'));
        $response = $this->client->getResponse();
        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains(static::GRID, $crawler->html());
        self::assertContains('Create Cms Form', $response->getContent());

        $response = $this->client->requestGrid(static::GRID);
        $gridRecords = self::getJsonResponseContent($response, Response::HTTP_OK);
        self::assertArrayHasKey('data', $gridRecords);
        self::assertCount(2, $gridRecords['data']);
    }

    public function testViewWithPreview()
    {
        /** @var CmsForm $cmsForm */
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'preview-enabled']);
        self::assertNotNull($cmsForm);

        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('d_wietor_cms_form_view', ['id' => $cmsForm->getId()])
        );

        $response = $this->client->getResponse();

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains('General', $response->getContent());
        self::assertContains('Fields', $response->getContent());
        self::assertContains('Reorder fields', $response->getContent());

        self::assertContains('preview/4f7554f2-4442-4baf-8d86-84cb33e1a125', $response->getContent());
        // Notifications are enabled and has at least one email set
        self::assertContains('davidwietor@gmail.com', $response->getContent());
        self::assertContains('Generated code', $response->getContent());
        self::assertContains('{{ d_wietor_form(&#039;preview-enabled&#039;) }}', $response->getContent());

        // Fields grid
        $response = $this->client->requestGrid([
            'gridName' => static::FIELDS_GRID,
            static::FIELDS_GRID . '[cmsFormId]' => $cmsForm->getId(),
        ]);
        $gridRecords = self::getJsonResponseContent($response, Response::HTTP_OK);
        self::assertArrayHasKey('data', $gridRecords);
        self::assertCount(4, $gridRecords['data']);
    }

    public function testViewWithoutPreviewAndNotifications()
    {
        /** @var CmsForm $cmsForm */
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'preview-disabled']);
        self::assertNotNull($cmsForm);

        $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('d_wietor_cms_form_view', ['id' => $cmsForm->getId()])
        );

        $response = $this->client->getResponse();

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains('Preview is not enabled. To enable it', $response->getContent());
        self::assertContains('Notifications are disabled or empty. To enable them', $response->getContent());
        self::assertContains('Generated code', $response->getContent());
        self::assertContains('{{ d_wietor_form(&#039;preview-disabled&#039;) }}', $response->getContent());
    }

    public function testCreateForm()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->getUrl('d_wietor_cms_form_create'));

        $form = $crawler->selectButton('Save and Close')->form();
        $form['form[name]'] = 'Contact Us';
        $form['form[alias]'] = 'contact-us';
        $form['form[previewEnabled]'] = true;
        $form['form[notificationsEnabled]'] = true;
        $form['form[notifications][0][email]'] = 'john@example.com';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        /** @var CmsForm $cmsForm */
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'contact-us']);

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains('Form saved', $crawler->html());
        self::assertNotNull($cmsForm);
        self::assertTrue($cmsForm->isPreviewEnabled());
        self::assertTrue($cmsForm->isNotificationsEnabled());
        self::assertCount(1, $cmsForm->getNotifications());
        $this->removeEntity($cmsForm);
    }

    public function testUpdateForm()
    {
        /** @var CmsForm $cmsForm */
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'preview-disabled']);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('d_wietor_cms_form_update', ['id' => $cmsForm->getId()])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['form[name]'] = 'Preview now enabled';
        $form['form[alias]'] = 'preview-now-enabled';
        $form['form[previewEnabled]'] = true;
        $form['form[notificationsEnabled]'] = true;
        $form['form[notifications][0][email]'] = 'john.doe@example.com';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'preview-now-enabled']);

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains('Form saved', $crawler->html());
        self::assertNotNull($cmsForm);
        self::assertTrue($cmsForm->isPreviewEnabled());
        self::assertTrue($cmsForm->isNotificationsEnabled());
        self::assertCount(1, $cmsForm->getNotifications());
    }

    public function testCreateField()
    {
        /** @var CmsForm $cmsForm */
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'preview-enabled']);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('d_wietor_cms_form_field_create', ['id' => $cmsForm->getId()])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['field[label]'] = 'Simple text field';
        $form['field[name]'] = 'simple-text-field';
        $form['field[type]'] = 'text';
        $form['field[placeholder]'] = 'This is a simple text field...';
        $form['field[css_class]'] = 'custom-css__container alert-box';
        $form['field[required]'] = true;

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        /** @var CmsFormField $field */
        $field = $this->getEntityBy(CmsFormField::class, ['name' => 'simple-text-field']);

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains('Field saved', $crawler->html());
        self::assertNotNull($field);
        self::assertEquals('Simple text field', $field->getLabel());
        self::assertEquals('text', $field->getType());
        self::assertTrue($field->getOption('required'));
        $attrOptions = $field->getOption('attr');
        self::assertEquals('This is a simple text field...', $attrOptions['placeholder']);
        self::assertEquals('custom-css__container alert-box', $attrOptions['class']);
        $this->removeEntity($field);
    }

    public function testUpdateField()
    {
        /** @var CmsFormField $field */
        $field = $this->getEntityBy(CmsFormField::class, ['name' => 'first-name']);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('d_wietor_cms_form_field_update', ['id' => $field->getId()])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['field[label]'] = 'First name edited';
        $form['field[name]'] = 'first-name-edited';
        $form['field[type]'] = 'textarea';
        $form['field[placeholder]'] = 'New placeholder...';
        $form['field[css_class]'] = '';
        $form['field[required]'] = false;

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        /** @var CmsFormField $field */
        $field = $this->getEntityBy(CmsFormField::class, ['name' => 'first-name-edited']);

        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains('Field saved', $crawler->html());
        self::assertNotNull($field);
        self::assertEquals('First name edited', $field->getLabel());
        self::assertEquals('textarea', $field->getType());
        self::assertFalse($field->getOption('required'));
        $attrOptions = $field->getOption('attr');
        self::assertEquals('New placeholder...', $attrOptions['placeholder']);
        self::assertEquals('', $attrOptions['class']);
    }

    public function testResponses()
    {
        /** @var CmsForm $cmsForm */
        $cmsForm = $this->getEntityBy(CmsForm::class, ['alias' => 'preview-enabled']);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getUrl('d_wietor_cms_form_responses', ['id' => $cmsForm->getId()])
        );
        $response = $this->client->getResponse();
        self::assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertContains(static::RESPONSES_GRID, $crawler->html());
        self::assertContains('View Form', $response->getContent());
        self::assertContains('Export', $response->getContent());

        $response = $this->client->requestGrid([
            'gridName' => static::RESPONSES_GRID,
            static::RESPONSES_GRID . '[cmsFormId]' => $cmsForm->getId(),
        ]);
        $gridRecords = self::getJsonResponseContent($response, Response::HTTP_OK);
        self::assertArrayHasKey('data', $gridRecords);
        self::assertCount(1, $gridRecords['data']);
        $firstRow = reset($gridRecords['data']);
        self::assertArrayHasKey('fieldResponses', $firstRow);
        self::assertContains('Last name', $firstRow['fieldResponses']);
        self::assertContains('NameDoe', $firstRow['fieldResponses']);
        self::assertContains('Email', $firstRow['fieldResponses']);
        self::assertContains('doe.xx@example.com', $firstRow['fieldResponses']);
        self::assertContains('Contact reason', $firstRow['fieldResponses']);
        self::assertContains('Have a complaint', $firstRow['fieldResponses']);
    }

    /**
     * @param object $entity
     */
    private function removeEntity($entity)
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();
        $entityManager->clear();
    }

    /**
     * @param string $class
     * @param array  $criteria
     *
     * @return null|object
     */
    private function getEntityBy(string $class, array $criteria)
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        return $entityManager->getRepository($class)->findOneBy($criteria);
    }
}
