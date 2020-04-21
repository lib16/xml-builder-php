<?php
require_once 'vendor/autoload.php';

// @todo for Travis CI
require_once 'vendor/lib16/utils/src/enums/css/LengthUnit.php';
require_once 'vendor/lib16/utils/src/enums/css/Media.php';
require_once 'vendor/lib16/utils/src/enums/mime/StyleType.php';

require_once 'src/Adhoc.php';
require_once 'src/Attributes.php';
require_once 'src/ProcessingInstruction.php';
require_once 'src/Xml.php';
require_once 'src/XmlWrapper.php';
require_once 'src/shared/ClassAttribute.php';
require_once 'src/shared/MediaAttribute.php';
require_once 'src/shared/Space.php';
require_once 'src/shared/Target.php';
require_once 'src/shared/TargetAttribute.php';
require_once 'src/shared/TitleAttribute.php';
require_once 'src/shared/XmlAttributes.php';
require_once 'src/shared/XmlStylesheet.php';
require_once 'src/shared/XmlStylesheetInstruction.php';
require_once 'src/shared/xlink/Actuate.php';
require_once 'src/shared/xlink/Show.php';
require_once 'src/shared/xlink/Type.php';
require_once 'src/shared/xlink/XLink.php';
require_once 'src/shared/xlink/XLinkConstants.php';
require_once 'tests/xmlClasses.php';