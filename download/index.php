<?php
	require_once('../template.php');

	$releases = simplexml_load_file("releases.xml")->release;
	$release = null;

	if (isset($_GET["version"])) {
		$version = $_GET["version"];
		foreach ($releases as $r) {
			if (strcmp($r["version"], $version) == 0) {
				$release = $r;
				break;
			}
		}
	}

	if (!isset($release)) {
		$release = $releases[0];
	}

	$latest = ($release == $releases[0]);

	$fixedBugs = simplexml_load_file("fixed-bugs/".$release["version"].".xml")->bug;

	$modelingTools = $release->eclipse["distribution"];

	$modelingToolsWin = $modelingTools."win32.zip";
	$modelingToolsWin64 = $modelingTools."win32-x86_64.zip";
	//$modelingToolsMac = $modelingTools."macosx-cocoa.tar.gz";
	$modelingToolsMac64 = $modelingTools."macosx-cocoa-x86_64.tar.gz";
	$modelingToolsLinux = $modelingTools."linux-gtk.tar.gz";
	$modelingToolsLinux64 = $modelingTools."linux-gtk-x86_64.tar.gz";

	$version = $release["version"];
	$breadCrumb = "";
	if (!$latest) {
		$breadCrumb = $version."/";
	}

	$distributions = (strcmp($release["distributions"], "no") != 0);
	$jars = (strcmp($release["jars"], "yes") == 0);

	$downloadUrl = "http://www.eclipse.org/downloads/download.php?file=/epsilon/".$breadCrumb."distributions/eclipse-epsilon-".$version."-";

	$downloadWin = $downloadUrl."win32.zip";
	$downloadWin64 = $downloadUrl."win32-x86_64.zip";
	$downloadMac = $downloadUrl."macosx-cocoa.zip";
	$downloadMac64 = $downloadUrl."macosx-cocoa-x86_64.zip";

	if ($version == "1.2") {
		$downloadLinux = $downloadUrl."linux-gtk.zip";
		$downloadLinux64 = $downloadUrl."linux-gtk-x86_64.zip";
	}
	else {
		$downloadLinux = $downloadUrl."linux-gtk.tar.gz";
		$downloadLinux64 = $downloadUrl."linux-gtk-x86_64.tar.gz";
	}

	$updateSite = "http://download.eclipse.org/epsilon/".$breadCrumb."updates/";

	function getVisitorPlatform()
	{
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];

	    if (preg_match('/linux/i', $u_agent)) {
	        return 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        return 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        return 'windows';
	    }
	    else {
	    	return 'unknown';
	    }
	}

	function getStyle($os) {
		$platform = getVisitorPlatform();
		if ($platform == "unknown" || $platform==$os) {
			return "btn btn-primary btn-medium";
		}
		else {
			return "btn btn-medium";
		}
	}

	function onWindows() {
		$platform = getVisitorPlatform();
		if ($platform == "unknown" || $platform=="windows") {
			return true;
		}
		else return false;
	}

	function onMac() {
		$platform = getVisitorPlatform();
		if ($platform == "unknown" || $platform=="mac") {
			return true;
		}
		else return false;
	}

	function jar($flavour, $version, $interim, $src) {
		global $breadCrumb;
		$srcLabel = "";
		if ($src) { $srcLabel = "-src"; }

		$jarFolder = "jars";
		if ($interim) { $jarFolder = "interim-jars"; }

		$filename = "epsilon-".$version."-".$flavour.$srcLabel.".jar";
		$link = "http://www.eclipse.org/downloads/download.php?file=/epsilon/".$breadCrumb.$jarFolder."/".$filename;
		return "<a href='".$link."'>".$filename."</a>";
	}

	h();
?>

<?if (false) {?>
<div class="row">
	<div class="span12">
	<div class="alert alert-info" style1="font-weight:normal; background-color: rgb(214,238,247); color: rgb(24,136,173); border-color: rgb(181,233,241)">
		We are currently in the middle of releasing a new stable version of Epsilon (1.2) so until that's done, some links/update sites may not work as expected.
		In the meantime, please <a href="index.php?version=1.1_SR1">download version 1.1_SR1 instead</a>.
    </div>
    </div>
</div>
<?}?>

<!--div class="row">
	<div class="span12">
	<div class="alert alert-info" style1="font-weight:normal; background-color: rgb(214,238,247); color: rgb(24,136,173); border-color: rgb(181,233,241)">
		If you have recently installed v1.1 or have downloaded one of its pre-bundled distributions,
		please upgrade to version 1.1_SR1 as v1.1 contained two
		<a href="https://bugs.eclipse.org/bugs/show_bug.cgi?id=416920">critical</a> <a href="https://bugs.eclipse.org/bugs/show_bug.cgi?id=416918">bugs</a> related to Eugenia.
		We apologise for any inconvenience caused.
    </div>
    </div>
</div-->

<div class="row">
	<!-- main part -->
	<div class="span8">
		<h1 class="page-header">Download</h1>
		<div class="row">
			<div class="span12">
				<!--
				<div class="alert alert-info alert-block">
					<button type="button" class="close" data-dismiss="alert">��</button>
					If you've downloaded one of the 1.0 distributions or installed 1.0 from the main update site before Friday Nov 9,
					please update Epsilon from the stable update site (or download a fresh copy of the distributiuon) to pick up a fix for <a href="https://bugs.eclipse.org/bugs/show_bug.cgi?id=393941">bug 393941</a>.
				</div>
				 -->

				<div class="tabbable" style="margin-bottom: 0px;">
				  <ul class="nav nav-tabs">
				  	<?if($distributions){?>
				    <li class="active"><a href="#distributions" data-toggle="tab"><h4>Distributions</h4></a></li>
				    <?}?>
				    <li <?if(!$distributions){ echo 'class="active"';}?>><a href="#updatesites" data-toggle="tab"><h4>Update Sites</h4></a></li>
				    <?if ($latest){?>
				    <li><a href="#marketplace" data-toggle="tab"><h4>Marketplace</h4></a></li>
				    <?}?>
				    <li><a href="#sourcecode" data-toggle="tab"><h4>Source code</h4></a></li>
				    <?if ($jars){?>
				    <li><a href="#jars" data-toggle="tab"><h4>JARs</h4></a></li>
				    <?}?>
				    <li><a href="#releasenotes" data-toggle="tab"><h4>Release notes</h4></a></li>
				    <li><a href="#versions" data-toggle="tab"><h4>All versions</h4></a></li>
				  </ul>
				    <div class="tab-content">
				    	<?if($distributions){?>
  						<div id="distributions" class="tab-pane active">
  							<p>
  							Ready-to-use Eclipse <?=$release->eclipse["name"]?> (<?=$release->eclipse["version"]?>) distributions containing a stable version of Epsilon (v<?=$version?>), EMF, GMF, and Emfatic. You will only need a <a href="http://www.oracle.com/technetwork/java/index.html">Java Runtime Environment</a>.
  							</p>
								<?if (onMac()){?>
								<div class="alert alert-info alert-block">
									<b>Warning:</b> If Mac OS X complains that Eclipse is damaged, you can either set the "Allow apps downloaded from" setting to "Anywhere" in the "Security and Privacy" preferences panel (this is only needed when you run Eclipse for the first time - you can disable this later), or run Eclipse from a terminal (i.e. ./Eclipse.app/Contents/MacOS/eclipse).
								</div>
								<?}?>
  							<p style="padding-top:15px;padding-bottom:15px">
							<a class="btn <?=getStyle('windows')?>" href="<?=$downloadWin?>">Windows 32bit</a>
							<a class="btn <?=getStyle('windows')?>" href="<?=$downloadWin64?>">Windows 64bit</a>
							<!--a class="btn <?=getStyle('mac')?>" href="<?=$downloadMac?>">Mac OS X 32bit</a-->
							<a class="btn <?=getStyle('mac')?>" href="<?=$downloadMac64?>">Mac OS X 64bit</a>

							<a class="btn <?=getStyle('linux')?>" href="<?=$downloadLinux?>">Linux 32bit</a>
							<a class="btn <?=getStyle('linux')?>" href="<?=$downloadLinux64?>">Linux 64bit</a>

							</p>
							<?if (onWindows()){?>
							<p><b>Note for Windows users:</b> Please make sure that you extract the downloaded distributions close to the root of a drive (e.g. C:/D:) as
							the maximum path length on Windows may not exceed 256 characters.
							</p>
							<?}?>
							</p>
							<?if (onMac() && strcmp($version, "1.0") == 0){?>
							<p><b>Note for Mac OSX Snow Leopard users:</b> The above distributions require Java 1.7 which is not
							available for Mac OSX Snow Leopard. To assemble a 1.6-compatible version of the Epsilon distribution,
							please download one of the distributions above, and re-install Emfatic from the following update site:
							<?=Epsilon::getEmfaticUpdateSite()?>
							</p>
							<?}?>
  						</div>
  						<?}?>
  						<div id="updatesites" class="tab-pane <?if(!$distributions){ echo "active";}?>">
  							<form class="form-horizontal" style="padding-left:1px">
  							<div class="control-group">
	  							<div class="input-prepend input-append">
								  <span class="add-on"><div class="span2">Stable</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="<?=$updateSite?>"/>
								</div>

								<?if ($latest){?>
	  							<div class="input-prepend input-append" style="padding-top:25px">
								  <span class="add-on"><div class="span2">Interim *</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="<?=Epsilon::getInterimUpdateSite()?>"/>
								</div>

								<p>

								<br>
								*<a href="https://bugs.eclipse.org/bugs/buglist.cgi?query_format=advanced;field0-0-0=status_whiteboard;bug_status=RESOLVED;bug_status=VERIFIED;type0-0-0=equals;value0-0-0=interim;product=epsilon">
								Bugs fixed in the latest interim version (compared to the latest stable version)
								</a>

								</p>
								<?}?>

								<h4 style="padding-top:10px;padding-bottom:10px">Dependencies (optional)</h4>

								<div class="input-prepend input-append">
								  <span class="add-on"><div class="span2">Emfatic</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="<?=Epsilon::getEmfaticUpdateSite()?>">
								</div>
								<div class="input-prepend input-append" style="padding-top:25px">
								  <span class="add-on"><div class="span2">GMF</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="Install through the Help->Install Modeling Components menu.">
								</div>

							</div>
							</form>
							<h4>What do I do with these?</h4>
							<p>
							The development tools of Epsilon come as a set of Eclipse plugins and therefore, to install Epsilon you need to download and install a <a href="http://www.oracle.com/technetwork/java/index.html">Java Runtime Environment</a> and Eclipse
							(including EMF, GMF and Emfatic in order to use the full range of its capabilities) first.
							The Eclipse <?=$release->eclipse["name"]?> Modeling Tools distribution contains most of the necessary prerequisites for Epsilon and is available for the following platforms.
							</p>
							<p style="padding-top:15px;padding-bottom:15px">
							<a class="btn <?=getStyle('windows')?>" href="<?=$modelingToolsWin?>">Windows 32bit</a>
							<a class="btn <?=getStyle('windows')?>" href="<?=$modelingToolsWin64?>">Windows 64bit</a>
							<!--a class="btn <?=getStyle('mac')?>" href="<?=$modelingToolsMac?>">Mac OS X 32bit</a-->
							<a class="btn <?=getStyle('mac')?>" href="<?=$modelingToolsMac64?>">Mac OS X 64bit</a>
							<a class="btn <?=getStyle('linux')?>" href="<?=$modelingToolsLinux?>">Linux 32bit</a>
							<a class="btn <?=getStyle('linux')?>" href="<?=$modelingToolsLinux64?>">Linux 64bit</a>
							</p>
							Once you have downoaded one of the Modeling distributions above, you will need to
							<ol>
								<li>Install GMF through the <i>Help->Install Modeling Components</i> menu of Eclipse
								<li>Install Emfatic through the <i>Help->Install New Software</i> menu of Eclipse using the Emfatic update site above
								<li>Install Epsilon through the <i>Help->Install New Software</i> menu of Eclipse using one of the Epsilon update sites (stable or interim/bleeding edge)
							</ol>

							<p>If you are not familiar with Eclipse/plugin installation, <a href="http://www.vogella.com/articles/Eclipse/article.html#install">this tutorial</a> provides an excellent crash course.</p>

							<h4>Which features should I install?</h4>

							<p>
							If you are a first-time user, we recommend installing them all. Otherwise, you may want to install only those that you need:
							</p>
							<ul>
							    <li><i>Epsilon Core:</i> provides the execution engines required to run E*L scripts and <a href="../doc/eunit/">EUnit</a> test suites.
							    <li><i>Epsilon Core Development Tools:</i> provides the development tools required to write new E*L scripts (editors, EUnit test results view, <a href="../doc/workflow/">Ant tasks</a>...).
							    <li><i>Epsilon EMF Integration:</i> provides the Epsilon Model Connectivity driver required to use EMF-based models in Epsilon.
							    <li><i>Epsilon Development Tools for EMF:</i> provides useful tools for developing E*L scripts that work with EMF-based models, such as <a href="../doc/exeed">Exeed</a>, <a href="../doc/modelink/">ModeLink</a>, EMF model comparison for EUnit test suites and so on.
							    <li><i>Epsilon Validation Language EMF Integration:</i> allows for integrating EVL scripts with the standard EMF model validation facilities.
							    <li><i>Epsilon Wizard Language EMF Integration:</i> allows for invoking EWL wizards on the appropriate elements in an EMF model, from the standard tree-based editors generated by EMF.
							    <li><i>Epsilon Wizard Language GMF Integration:</i> allows for invoking EWL wizards on the appropriate elements in an EMF model, from the graphical editors generated by GMF.
							    <li><i><a href="../doc/eugenia/">Eugenia</a>:</i> provides an environment for easily creating GMF editors from a set of text files.
							    <li><i><a href="../doc/hutn/">Human Usable Text Notation</a> Core:</i> provides the EMC driver required to load models written in the OMG HUTN textual notation.
							    <li><i>Human Usable Text Notation Development Tools:</i> provides an editor for models written in OMG HUTN.
							    <li><i>Epsilon <a href="../doc/concordance/">Concordance</a>:</i> provides a tool that detects, reconciles and reports broken cross-resource EMF references.
							</ul>
  						</div>
  						<?if ($latest){?>
  						<div id="marketplace" class="tab-pane">
  							Drag and drop into a running Eclipse <?=$release->eclipse["name"]?> workspace to
  							<a style="position:relative;top:-2px" href="http://marketplace.eclipse.org/marketplace-client-intro?mpc_install=400" title="install"><img src="http://marketplace.eclipse.org/sites/all/modules/custom/marketplace/images/installbutton.png"/></a> the latest stable version (v <?=$version?>) of Epsilon.
  						</div>
  						<?}?>
  						<div id="sourcecode" class="tab-pane">
  							<p>
  							The source code of Epsilon is stored in the following Git repository.
  							</p>
  							<form class="form-horizontal" style="padding-left:1px">
  							<div class="control-group">
  								<?if ($latest){?>
	  							<div class="input-prepend input-append" style="padding-top:25px">
								  <span class="add-on"><div class="span2">Users</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="git://git.eclipse.org/gitroot/epsilon/org.eclipse.epsilon.git">
								</div>
	  							<div class="input-prepend input-append" style="padding-top:25px">
								  <span class="add-on"><div class="span2">Committers</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="ssh://git.eclipse.org/gitroot/epsilon/org.eclipse.epsilon.git">
								</div>
								<?}?>
	  							<div class="input-prepend input-append" style="padding-top:25px">
								  <span class="add-on"><div class="span2">Release tag</div></span>
								  <input class="span9" id="appendedPrependedInput" type="text" value="https://git.eclipse.org/c/epsilon/org.eclipse.epsilon.git/tag/?id=<?=$version?>">
								</div>
							</div>
							</form>
							<br/>
							<p>
								<b>Note:</b> If you need to modify and re-build the parsers of the Epsilon languages, you will also need to clone the following repository next to the Epsilon Git repository on your machine: <code>https://github.com/epsilonlabs/epsilon-antlr-dev.git</code>
							</p>
  						</div>

  						<?if ($jars){?>
  						<div id="jars" class="tab-pane">
	  						<ul class="nav nav-pills">
							    <li class="active"><a href="#stablejars" data-toggle="tab">Stable</a></li>
							    <li><a href="#interimjars" data-toggle="tab">Interim</a></li>
							</ul>
							<div class="tab-content">
								<div id="stablejars" class="tab-pane active">
									<p>
			  							Plain old JARs you can use to embed the latest <b>stable</b> version of Epsilon (<?=$version?>)
			  							<a href="../examples/index.php?example=org.eclipse.epsilon.examples.standalone">as a library</a> in your Java or Android application. You can also use Maven: see instructions below the table.
			  						</p>
	  								<?$jarsUrl = "http://www.eclipse.org/downloads/download.php?file=/epsilon/".$breadCrumb."jars";?>
	  								<?include("jars/".$version.".php");?>
                                     <p>
                                     Since 1.4, these JARs are also available from Maven Central. For instance, to use the <code>epsilon-core</code> JAR from your <code>pom.xml</code>:
                                     <pre>&lt;dependencies&gt;
  ...
  &lt;dependency&gt;
    &lt;groupId&gt;org.eclipse.epsilon&lt;/groupId&gt;
    &lt;artifactId&gt;epsilon-core&lt;/artifactId&gt;
    &lt;version&gt;1.4.0&lt;/version&gt;
  &lt;/dependency&gt;
  ...
&lt;/dependencies&gt;</pre>
                                     </p>
	  							</div>
	  							<div id="interimjars" class="tab-pane">
									<p>
			  							Plain old JARs you can use to embed the latest <b>interim</b> version of Epsilon
			  							<a href="../examples/index.php?example=org.eclipse.epsilon.examples.standalone">as a library</a> in your Java or Android application. You can also use Maven: see instructions below the table.
			  						</p>
			  						<?$jarsUrl = "http://www.eclipse.org/downloads/download.php?file=/epsilon/".$breadCrumb."interim-jars";?>
			  						<?include("jars/interim.php");?>
									<p>
									You can use the latest SNAPSHOTs at the <a href="https://oss.sonatype.org">Sonatype OSSRH</a> repository. For instance, to use the 1.4 interim <code>epsilon-core</code> JAR from your <code>pom.xml</code>:
									<pre>&lt;repositories&gt;
    &lt;repository&gt;
      &lt;id&gt;ossrh-snapshots&lt;/id&gt;
      &lt;url&gt;https://oss.sonatype.org/content/repositories/snapshots&lt;/url&gt;
    &lt;/repository&gt;
&lt;/repositories&gt;
...
&lt;dependencies&gt;
  &lt;dependency&gt;
    &lt;groupId&gt;org.eclipse.epsilon&lt;/groupId&gt;
    &lt;artifactId&gt;epsilon-core&lt;/artifactId&gt;
    &lt;version&gt;1.4.0-SNAPSHOT&lt;/version&gt;
  &lt;/dependency&gt;
&lt;/dependencies&gt;</pre>
									</p>
	  							</div>
	  						</div>
  						</div>
  						<?}?>

  						<div id="releasenotes" class="tab-pane">
  						<p>
  						Version <?=$version?> fixes the bugs and implements the enhancement requests below.
  						</p>
  						<table class="table table-striped">
  							<thead>
  								<tr>
  									<th>#</th>
  									<th>Description</th>
  									<th>Reporter</th>
  								</tr>
  							</thead>
  							<tbody>
	  							<?foreach ($fixedBugs as $fixedBug){?>
	  							<tr>
	  								<td><?=$fixedBug->bug_id?></td>
	  								<td><a href="https://bugs.eclipse.org/bugs/show_bug.cgi?id=<?=$fixedBug->bug_id?>"><?=$fixedBug->short_desc?></a></td>
	  								<td><?=$fixedBug->reporter?></td>

	  							</tr>
	  							<?}?>
  							</tbody>
						</table>

  						</div>
  						<div id="versions" class="tab-pane">
  						<table class="table table-striped">
  							<thead>
  								<tr>
  									<th>Version</th>
  									<th>Eclipse</th>
  									<th>Released</th>
  									<th>Notes</th>
  								</tr>
  							</thead>
  							<tbody>
  							<?foreach ($releases as $r){?>
  							<tr>
  								<td>
  									<a href="?version=<?=$r["version"]?>"><?=$r["version"]?></a>
  								</td>
  								<td>
  									<?=$r->eclipse["version"]?> (<?=$r->eclipse["name"]?>)
  								</td>
  								<td>
  									<?=$r["released"]?>
  								</td>
  								<td>
  									<?=$r->notes?>
  								</td>
  							</tr>
  							<?}?>
  							</tbody>
  						</table>
  						</ul>
  						</div>
  					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?f();?>
