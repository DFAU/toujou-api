<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Schema;


use DFAU\Convergence\Schema;

class PagesJsonApiSchema extends Schema
{
    public function __construct()
    {
        parent::__construct(
            [new Schema\InterGraphResourceRelation(new Schema\ExpressionIdentifier('resource["type"]~"_"~resource["id"]'))],
            [
                new Schema\IntraGraphResourceRelation(
                    new Schema\ExpressionQualifier('resource["type"] != "pages"'),
                    new Schema\StringPropertyPathReferenceList('resource[attributes][pid]'),
                    new Schema\ExpressionIdentifier('resource["type"] == "pages" ? resource["id"] : ""')
                )
            ],
            [new Schema\ResourcePropertiesExtractor(
                new Schema\ExpressionQualifier('resource["type"] && resource["id"]'),
                new Schema\PropertyPathPropertyList('resource[attributes]'),
                new Schema\ExpressionQualifier('key not in ["pid","sorting"]')
            )]
        );
    }
}
