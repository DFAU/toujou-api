<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Schema;

use DFAU\Convergence\Schema\InterGraphResourceRelation;
use DFAU\Convergence\Schema\ExpressionIdentifier;
use DFAU\Convergence\Schema\IntraGraphResourceRelation;
use DFAU\Convergence\Schema\ExpressionQualifier;
use DFAU\Convergence\Schema\StringPropertyPathReferenceList;
use DFAU\Convergence\Schema\ResourcePropertiesExtractor;
use DFAU\Convergence\Schema\PropertyPathPropertyList;
use DFAU\Convergence\Schema;

class PagesJsonApiSchema extends Schema
{
    public function __construct()
    {
        parent::__construct(
            [new InterGraphResourceRelation(new ExpressionIdentifier('resource["type"]~"_"~resource["id"]'))],
            [
                new IntraGraphResourceRelation(
                    new ExpressionQualifier('resource["type"] != "pages"'),
                    new StringPropertyPathReferenceList('resource[attributes][pid]'),
                    new ExpressionIdentifier('resource["type"] == "pages" ? resource["id"] : ""')
                ),
                new IntraGraphResourceRelation(
                    new ExpressionQualifier('resource["relationships"]'),
                    new JsonApiResourceRelationReferenceList(new ExpressionIdentifier('resource["type"]~"_"~resource["id"]')),
                    new ExpressionIdentifier('resource["type"]~"_"~resource["id"]')
                ),
            ],
            [new ResourcePropertiesExtractor(
                new ExpressionQualifier('resource["type"] && resource["id"]'),
                new PropertyPathPropertyList('resource[attributes]'),
                new ExpressionQualifier('key not in ["pid","sorting"]')
            )]
        );
    }
}
