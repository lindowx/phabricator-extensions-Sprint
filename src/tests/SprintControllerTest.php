<?php
final class SprintControllerTest extends SprintTestCase {

  public function testshouldAllowPublic()  {
    $stub = $this->getMockForAbstractClass('SprintController');

    $this->assertTrue($stub->shouldAllowPublic());
  }

  public function testbuildSideNavView() {
    $stub = $this->getMockForAbstractClass('SprintController');
    $user = $this->generateNewTestUser();
    $uri = new PhutilURI('/sprint/');
    $nav = $stub->buildSideNavView($for_app = false, $user, $uri);
    $this->assertInstanceOf('AphrontSideNavFilterView', $nav);
  }

  public function testgetCrumbs() {
    $projectobj = new PhabricatorProject();
    $viewer = $this->generateNewTestUser();
    $project = $projectobj->initializeNewProject($viewer);
    $dv = new SprintDataViewController();
    $can_create = true;
    $crumbs = $dv->getCrumbs($project, $can_create);
    $this->assertInstanceOf('PhabricatorCrumbsView', $crumbs);
  }

  public function testgetBurndownView() {
    $projectobj = new PhabricatorProject();
    $viewer = $this->generateNewTestUser();
    $project = $projectobj->initializeNewProject($viewer);
    $request = new AphrontRequest('phab.wmde.de', '/sprint/view/18');
    $dv = new SprintDataViewController();
    $burndownview = $dv->getBurndownView($request, $project, $viewer);
    $this->assertInstanceOf('SprintDataView', $burndownview);
  }

  public function testgetErrorBox() {
    $e = new BurndownException();
    $dv = new SprintDataViewController();
    $errorbox = $dv->getErrorBox($e);
    $this->assertInstanceOf('AphrontErrorView', $errorbox);
  }

  public function testprocessRequestFail() {
    $dvcontroller = new SprintDataViewController();
    $sprint = new SprintApplication();
    $dvcontroller->setCurrentApplication($sprint);
    $request = new AphrontRequest('phab.wmde.de', '/sprint/view/18');
    $data['id'] =  3;
    $request->setRequestdata($data);
    $viewer = $this->generateNewTestUser();
    $request->setUser($viewer);
    $dvcontroller->willProcessRequest($data);
    $dvcontroller->setRequest($request);
    $response = $dvcontroller->processRequest();
    $this->assertInstanceOf('Aphront404Response', $response);
  }

  public function testprocessRequestListController() {
    $lcontroller = new SprintListController();
    $sprint = new SprintApplication();
    $lcontroller->setCurrentApplication($sprint);
    $request = new AphrontRequest('phab.wmde.de', '/sprint/view/18');
    $viewer = $this->generateNewTestUser();
    $request->setUser($viewer);
    $lcontroller->setRequest($request);
    $response = $lcontroller->processRequest();
    $this->assertInstanceOf('AphrontWebpageResponse', $response);
  }
}
