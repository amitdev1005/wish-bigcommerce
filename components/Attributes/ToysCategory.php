<?php return $arr = array (
  'Toys' => 
  array (
    'shortDescription' => 
    array (
      'name' => 'shortDescription',
      'maxOccurs' => '1',
      'minOccurs' => '1',
      'documentation' => 'Overview of the key selling points of the item, marketing content, and highlights in paragraph form. For SEO purposes, repeat the product name and relevant keywords here.',
      'requiredLevel' => 'Required',
      'displayName' => 'Description',
      'group' => 'Basic',
      'rank' => '8000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'keyFeatures' => 
    array (
      'name' => 'keyFeatures',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Key features will appear as bulleted text on the item page and in search results. Key features help the user understand the benefits of the product with a simple and clean format. We highly recommended using three or more key features.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Key Features',
      'group' => 'Basic',
      'rank' => '9000',
    ),
    'brand' => 
    array (
      'name' => 'brand',
      'maxOccurs' => '1',
      'minOccurs' => '1',
      'documentation' => 'Name, term, design or other feature that distinguishes one seller\'s product from those of others. This can be the name of the company associated with the product, but not always. If item does not have a brand, use "Unbranded".',
      'requiredLevel' => 'Required',
      'displayName' => 'Brand',
      'group' => 'Basic',
      'rank' => '11000',
      'dataType' => 'string',
      'maxLength' => '60',
      'minLength' => '1',
    ),
    'manufacturer' => 
    array (
      'name' => 'manufacturer',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Manufacturer is the maker of the product. This is the name of the company that produces the product, not necessarily the brand name of the item. For some products, the manufacturer and the brand may be the same.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Manufacturer',
      'group' => 'Basic',
      'rank' => '12000',
      'dataType' => 'string',
      'maxLength' => '60',
      'minLength' => '1',
    ),
    'manufacturerPartNumber' => 
    array (
      'name' => 'manufacturerPartNumber',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'MPN uniquely identifies the product to its manufacturer. For many products this will be identical to the model number. Some manufacturers distinguish part number from model number. Having this information allows customers to search for items on the site and informs product matching.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Manufacturer Part Number',
      'group' => 'Basic',
      'rank' => '13000',
      'dataType' => 'string',
      'maxLength' => '60',
      'minLength' => '1',
    ),
    'modelNumber' => 
    array (
      'name' => 'modelNumber',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Model numbers allow manufacturers to keep track of each hardware device and identify or replace the proper part when needed. Model numbers are often found on the bottom, back, or side of a product. Having this information allows customers to search for items on the site and informs product matching.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Model Number',
      'group' => 'Basic',
      'rank' => '14000',
      'dataType' => 'string',
      'maxLength' => '60',
      'minLength' => '1',
    ),
    'multipackQuantity' => 
    array (
      'name' => 'multipackQuantity',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The number of identical, individually packaged-for-sale items. If an item does not contain other items, does not contain identical items, or if the items contained within cannot be sold individually, the value for this attribute should be "1." Examples: (1) A single bottle of 50 pills has a "Multipack Quantity" of "1." (2) A package containing two identical bottles of 50 pills has a "Multipack Quantity" of 2. (3) A 6-pack of soda labelled for individual sale connected by plastic rings has a "Multipack Quantity" of "6." (4) A 6-pack of soda in a box whose cans are not marked for individual sale has a "Multipack Quantity" of "1." (5) A gift basket of 5 different items has a "Multipack Quantity" of "1."',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Multipack Quantity',
      'group' => 'Basic',
      'rank' => '15500',
      'dataType' => 'integer',
      'totalDigits' => '4',
    ),
    'countPerPack' => 
    array (
      'name' => 'countPerPack',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The number of identical items inside each individual pack given by the "Multipack Quantity" attribute. Examples: (1) A single bottle of 50 pills has a "Count Per Pack" of "50." (2) A package containing two identical bottles of 50 pills has a "Count Per Pack" of 50. (3) A 6-pack of soda labelled for individual sale connected by plastic rings has a "Count Per Pack" of "1." (4) A 6-pack of soda in a box whose cans are not marked for individual sale has a "Count Per Pack" of "6." (5) A gift basket of 5 different items has a "Count Per Pack" of "1."',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Count Per Pack',
      'group' => 'Basic',
      'rank' => '15750',
      'dataType' => 'integer',
      'totalDigits' => '4',
    ),
    'count' => 
    array (
      'name' => 'count',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The total number of identical items in the package or box; a result of the multiplication of Multipack Quantity by Count Per Pack. Examples: (1) A single bottle of 50 pills has a "Total Count" of 50. (2) A package containing two identical bottles of 50 pills has a "Total Count" of 100. (3) A gift basket of 5 different items has a "Total Count" of 1.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Total Count',
      'group' => 'Basic',
      'rank' => '15800',
      'dataType' => 'string',
      'maxLength' => '7',
      'minLength' => '1',
    ),
    'pieceCount' => 
    array (
      'name' => 'pieceCount',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The number of small pieces, slices, or different items within the product. Piece Count applies to things such as puzzles, building block sets, and products that contain multiple different items (such as tool sets, dinnerware sets, gift baskets, art sets, makeup kits, or shaving kits). EXAMPLE: (1) A 500-piece puzzle has a "Piece Count" of 500. (2) A 105-Piece Socket Wrench set has a piece count of "105." (3) A gift basket of 5 different items has a "Piece Count" of 5.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Number of Pieces',
      'group' => 'Basic',
      'rank' => '15825',
      'dataType' => 'integer',
      'totalDigits' => '20',
    ),
    'mainImageUrl' => 
    array (
      'name' => 'mainImageUrl',
      'maxOccurs' => '1',
      'minOccurs' => '1',
      'documentation' => 'Main image of the item. Image URL must end in the file name. Image URL must be static and have no redirections. Preferred file type: JPEG Accepted file types: JPG, PNG, GIF, BMP Recommended image resolution: 3000 x 3000 pixels at 300 ppi. Minimum image size requirements: Zoom capability: 2000 x 2000 pixels at 300 ppi. Non-zoom minimum: 500 x 500 pixels at 72 ppi. Maximum file size: 2 MB.',
      'requiredLevel' => 'Required',
      'displayName' => 'Main Image URL',
      'group' => 'Images',
      'rank' => '16000',
      'dataType' => 'anyURI',
    ),
    'productSecondaryImageURL' => 
    array (
      'name' => 'productSecondaryImageURL',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Secondary images of the item. Image URL must end in the file name. Image URL must be static and have no redirections. Preferred file type: JPEG Accepted file types: JPG, PNG, GIF, BMP Recommended image resolution: 3000 x 3000 pixels at 300 ppi. Minimum image size requirements: Zoom capability: 2000 x 2000 pixels at 300 ppi. Non-zoom minimum: 500 x 500 pixels at 72 ppi. Maximum file size: 2 MB.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Additional Image URL',
      'group' => 'Images',
      'rank' => '17000',
    ),
    'color' => 
    array (
      'name' => 'color',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Color as described by the manufacturer.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Color',
      'group' => 'Discoverability',
      'rank' => '19000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'colorCategory' => 
    array (
      'name' => 'colorCategory',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Select the color from a short list that best describes the general color of the item. This improves searchability as it allows customers to view items by color from the left navigation when they perform a search.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Color Category',
      'group' => 'Discoverability',
      'rank' => '20000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'colorCategoryValue',
          'maxOccurs' => 'unbounded',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'Beige',
            1 => 'Black',
            2 => 'Blue',
            3 => 'Bronze',
            4 => 'Brown',
            5 => 'Clear',
            6 => 'Gold',
            7 => 'Gray',
            8 => 'Green',
            9 => 'Multi-color',
            10 => 'Off-White',
            11 => 'Orange',
            12 => 'Pink',
            13 => 'Purple',
            14 => 'Red',
            15 => 'Silver',
            16 => 'White',
            17 => 'Yellow',
          ),
        ),
      ),
    ),
    'gender' => 
    array (
      'name' => 'gender',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Indicate whether this item is meant for a particular gender or meant to be gender-agnostic (unisex).',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Gender',
      'group' => 'Discoverability',
      'rank' => '21000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Women',
        1 => 'Men',
        2 => 'Girls',
        3 => 'Boys',
        4 => 'Unisex',
      ),
    ),
    'size' => 
    array (
      'name' => 'size',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Overall dimensions of an item. Used only for products that do not already have a more specific \'x size\' attribute, such as ring size or clothing size. ',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Size',
      'group' => 'Discoverability',
      'rank' => '23000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'ageGroup' => 
    array (
      'name' => 'ageGroup',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'General grouping of ages into commonly used demographic labels.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Age Group',
      'group' => 'Discoverability',
      'rank' => '24000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'ageGroupValue',
          'maxOccurs' => 'unbounded',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'Child',
            1 => 'Teen',
            2 => 'Adult',
          ),
        ),
      ),
    ),
    'ageRange' => 
    array (
      'name' => 'ageRange',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Minimum and Maximum Ages for a product. Note: Both Min. and Max. attributes will be the same Unit of Measure: Months, or Years. ',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Age Range',
      'group' => 'Discoverability',
      'rank' => '25000',
    ),
    'targetAudience' => 
    array (
      'name' => 'targetAudience',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The demographic for which the item is targeted.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Target Audience',
      'group' => 'Discoverability',
      'rank' => '29000',
    ),
    'educationalFocus' => 
    array (
      'name' => 'educationalFocus',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Is the item intended to improve a particular educational skill? ',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Educational Focus',
      'group' => 'Discoverability',
      'rank' => '30000',
    ),
    'skillLevel' => 
    array (
      'name' => 'skillLevel',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Is the product marketed or intended to be used by someone with a particular background or skill level? ',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Skill Level',
      'group' => 'Discoverability',
      'rank' => '31000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'awardsWon' => 
    array (
      'name' => 'awardsWon',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Use this attribute if the item has won any awards in its particular product category. ',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Awards Won',
      'group' => 'Discoverability',
      'rank' => '32000',
    ),
    'theme' => 
    array (
      'name' => 'theme',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'A dominant idea, meaning, or setting applied to an item. Used in a wide range of products including decorative objects, clothing, toys, and furniture. Can be an important selection criteria for consumers who want to achieve a particular ambiance for room décor or for a special occasion.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Theme',
      'group' => 'Discoverability',
      'rank' => '33000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'character' => 
    array (
      'name' => 'character',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'A person or entity portrayed in print or visual media. A character might be a fictional personality or an actual living person.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Character',
      'group' => 'Discoverability',
      'rank' => '34000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'activity' => 
    array (
      'name' => 'activity',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The general type of activity one might perform while wearing this garment.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Activity',
      'group' => 'Discoverability',
      'rank' => '34500',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'globalBrandLicense' => 
    array (
      'name' => 'globalBrandLicense',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'A brand name that is not owned by the product brand, but is licensed for the particular product. (Often character and media tie-ins and promotions.)',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Brand License',
      'group' => 'Discoverability',
      'rank' => '35000',
    ),
    'numberOfPlayers' => 
    array (
      'name' => 'numberOfPlayers',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Number of players that can participate in the game.	',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Number of Players',
      'group' => 'Discoverability',
      'rank' => '36000',
    ),
    'assembledProductLength' => 
    array (
      'name' => 'assembledProductLength',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The length of the fully assembled product. NOTE: This information is shown on the item page.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Assembled Product Length',
      'group' => 'Dimensions',
      'rank' => '37000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'in',
            1 => 'ft',
          ),
        ),
      ),
    ),
    'assembledProductWidth' => 
    array (
      'name' => 'assembledProductWidth',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The width of the fully assembled product. NOTE: This information is shown on the item page.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Assembled Product Width',
      'group' => 'Dimensions',
      'rank' => '38000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'in',
            1 => 'ft',
          ),
        ),
      ),
    ),
    'assembledProductHeight' => 
    array (
      'name' => 'assembledProductHeight',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The height of the fully assembled product. NOTE: This information is shown on the item page.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Assembled Product Height',
      'group' => 'Dimensions',
      'rank' => '39000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'in',
            1 => 'ft',
          ),
        ),
      ),
    ),
    'assembledProductWeight' => 
    array (
      'name' => 'assembledProductWeight',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The weight of the fully assembled product. NOTE: This information is shown on the item page.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Assembled Product Weight',
      'group' => 'Dimensions',
      'rank' => '40000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'oz',
            1 => 'lb',
            2 => 'g',
            3 => 'kg',
          ),
        ),
      ),
    ),
    'variantGroupId' => 
    array (
      'name' => 'variantGroupId',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Required if item is a variant. Make up a number and/or letter code for “Variant Group ID” and add this to all variations of the same product. Partners must ensure uniqueness of their Variant Group IDs.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'variantAttributeNames',
          'value' => 'ANY',
        ),
      ),
      'displayName' => 'Variant Group ID',
      'group' => 'Item Variants',
      'rank' => '42000',
      'dataType' => 'string',
      'maxLength' => '300',
      'minLength' => '1',
    ),
    'variantAttributeNames' => 
    array (
      'name' => 'variantAttributeNames',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Designate all attributes by which the item is varying. Variant attributes are limited to those designated for each category. ',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'variantGroupId',
          'value' => 'ANY',
        ),
      ),
      'displayName' => 'Variant Attribute Names',
      'group' => 'Item Variants',
      'rank' => '43000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'variantAttributeName',
          'maxOccurs' => 'unbounded',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'color',
            1 => 'size',
            2 => 'countPerPack',
            3 => 'count',
            4 => 'flavor',
            5 => 'capacity',
            6 => 'shape',
            7 => 'sportsTeam',
            8 => 'character',
          ),
        ),
      ),
    ),
    'isPrimaryVariant' => 
    array (
      'name' => 'isPrimaryVariant',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Note whether item is intended as the main variant in a variant grouping. The primary variant will appear as the image when customers search and the first image displayed on the item page. This should be set as "Yes" for only one item in a group of variants.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Is Primary Variant',
      'group' => 'Item Variants',
      'rank' => '44000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'isProp65WarningRequired' => 
    array (
      'name' => 'isProp65WarningRequired',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Selecting "Y" indicates the product requires California\'s Proposition 65 special warning. Proposition 65 entitles California consumers to special warnings for products that contain chemicals known to the state of California to cause cancer and birth defects or other reproductive harm if certain criteria are met (such as quantity of chemical contained in the product). See the portions of the California Health and Safety Code related to Proposition 65 for more information.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Is Prop 65 Warning Required',
      'group' => 'Compliance',
      'rank' => '48000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'prop65WarningText' => 
    array (
      'name' => 'prop65WarningText',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'This is a particular statement legally required by the State of California for certain products to warn consumers about potential health dangers. See the portions of the California Health and Safety Code related to Proposition 65 to see what products require labels and to verify the text of your warning label.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'isProp65WarningRequired',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Prop 65 Warning Text',
      'group' => 'Compliance',
      'rank' => '49000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'smallPartsWarnings' => 
    array (
      'name' => 'smallPartsWarnings',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'To determine if any choking warnings are applicable, check current product packaging for choking warning message(s). Please indicate the warning number (0-6). 0 - No warning applicable; 1 - Choking hazard is a small ball; 2 - Choking hazard contains small ball; 3 - Choking hazard contains small parts; 4 - Choking hazard balloon; 5 - Choking hazard is a marble; 6 - Choking hazard contains a marble.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Small Parts Warning Code',
      'group' => 'Compliance',
      'rank' => '50000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'smallPartsWarning',
          'maxOccurs' => 'unbounded',
          'minOccurs' => '1',
          'dataType' => 'integer',
          'optionValues' => 
          array (
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
          ),
        ),
      ),
    ),
    'hasExpiration' => 
    array (
      'name' => 'hasExpiration',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Select Yes if product is labeled with any type of expiration or code date that indicates when product should no longer be consumed or no longer at best quality (e.g. Best If Used By,  Best By, Use By, etc. ). Some examples of items with expiration dates include food, cleaning supplies, beauty products, etc.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Has Expiration',
      'group' => 'Compliance',
      'rank' => '52000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'hasIngredientList' => 
    array (
      'name' => 'hasIngredientList',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Does your product have a list of ingredients OTHER than that provided with Drug Facts, Nutrition Facts, or Supplement Facts? If so, please provide EITHER the ingredients text or the URL to the image.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Has Ingredient List',
      'group' => 'Compliance',
      'rank' => '54000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'ingredientListImage' => 
    array (
      'name' => 'ingredientListImage',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'If your product contains a list of ingredients OTHER than that required with drug, supplement, or nutrition info, provide the URL of image. Provide the final destination image URL (no-redirects) that is publicly accessible (no username/password required) and ends in a proper image extension. Recommended file type: JPEG Accepted file types: JPG, PNG, GIF, BMP Maximum file size: 2 GB.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'hasIngredientList',
          'value' => 'Yes',
        ),
        1 => 
        array (
          'name' => 'ingredients',
          'value' => 'null',
        ),
      ),
      'displayName' => 'Ingredient List Image',
      'group' => 'Compliance',
      'rank' => '55000',
      'dataType' => 'anyURI',
    ),
    'batteryTechnologyType' => 
    array (
      'name' => 'batteryTechnologyType',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Please select the Battery Technology Type from the list provided. NOTE: If battery type is lead acid, lead acid (nonspillable), lithium ion, or lithium metal, please ensure that you have obtained a hazardous materials risk assessment through WERCS  before setting up your item. ',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'hasBatteries',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Contained Battery Type',
      'group' => 'Compliance',
      'rank' => '57000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Does Not Contain a Battery',
        1 => 'Alkaline',
        2 => 'Carbon Zinc',
        3 => 'Lead Acid',
        4 => 'Lead Acid (Nonspillable)',
        5 => 'Lithium Primary (Lithium Metal)',
        6 => 'Lithium Ion',
        7 => 'Magnesium',
        8 => 'Mercury',
        9 => 'Nickel Cadmium',
        10 => 'Nickel Metal Hydride',
        11 => 'Silver',
        12 => 'Thermal',
        13 => 'Other',
        14 => 'Multiple Types',
      ),
    ),
    'hasWarranty' => 
    array (
      'name' => 'hasWarranty',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Y indicates the item comes with a warranty. If an item has a warranty, then enter EITHER the warranty URL or the warranty text in the appropriate field.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Has Warranty',
      'group' => 'Compliance',
      'rank' => '62000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'warrantyURL' => 
    array (
      'name' => 'warrantyURL',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'If you indicated that your item has a warranty, provide either the Warranty URL or Warranty Text. The Warranty URL is the web location of the image, PDF, or link to the manufacturer\'s warranty page, showing the warranty and its terms, including the duration of the warranty. URLs must begin with http:// or https:// NOTE: Please remember to update the link and/or text of the warranty as the warranty changes. If supplying an image, provide the final destination image URL (no-redirects) that is publicly accessible (no username/password required) and ends in a proper image extension. Recommended file type: JPEG Accepted file types: JPG, PNG, GIF, BMP Maximum file size: 2 GB. If the Ingredients have been included in another image, you may repeat the URL here. ',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'warrantyText',
          'value' => 'null',
        ),
        1 => 
        array (
          'name' => 'hasWarranty',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Warranty URL',
      'group' => 'Compliance',
      'rank' => '63000',
      'dataType' => 'anyURI',
    ),
    'warrantyText' => 
    array (
      'name' => 'warrantyText',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'If you marked Y for "Has Warranty" provide the Warranty URL or Warranty Text (the full text of the warranty terms, including what is covered by the warranty and the duration of the warranty). NOTE: please remember to update the text of your warranty as your warranty changes.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'warrantyURL',
          'value' => 'null',
        ),
        1 => 
        array (
          'name' => 'hasWarranty',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Warranty Text',
      'group' => 'Compliance',
      'rank' => '64000',
      'dataType' => 'string',
      'maxLength' => '1000',
      'minLength' => '1',
    ),
    'requiresTextileActLabeling' => 
    array (
      'name' => 'requiresTextileActLabeling',
      'maxOccurs' => '1',
      'minOccurs' => '1',
      'documentation' => 'Select "Y" if your item contains wool or is one of the following: clothing (except for hats and shoes), handkerchiefs, scarves, bedding (including sheets, covers, blankets, comforters, pillows, pillowcases, quilts, bedspreads and pads (but not outer coverings for mattresses or box springs)), curtains and casements, draperies, tablecloths, napkins, doilies, floor coverings (rugs, carpets and mats), towels, washcloths, dishcloths, ironing board covers and pads, umbrellas, parasols, bats or batting, flags with heading or that are bigger than 216 square inches, cushions, all fibers, yarns and fabrics (but not packaging ribbons), furniture slip covers and other furniture covers, afghans and throws, sleeping bags, antimacassars (doilies), hammocks, dresser and other furniture scarves. For further information on these requirements, refer to the labeling requirements of the Textile Act. ',
      'requiredLevel' => 'Required',
      'displayName' => 'Requires Textile Act Labeling',
      'group' => 'Compliance',
      'rank' => '68500',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'countryOfOriginTextiles' => 
    array (
      'name' => 'countryOfOriginTextiles',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Use “Made in U.S.A. and Imported” to indicate manufacture in the U.S. from imported materials, or part processing in the U.S. and part in a foreign country. Use “Made in U.S.A. or Imported” to reflect that some units of an item originate from a domestic source and others from a foreign source. Use “Made in U.S.A.” only if all units were made completely in the U.S. using materials also made in the U.S. Use "Imported" if units are completely imported.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'requiresTextileActLabeling',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Country of Origin - Textiles',
      'group' => 'Compliance',
      'rank' => '69750',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'USA',
        1 => 'Imported',
        2 => 'USA and Imported',
        3 => 'USA or Imported',
      ),
    ),
    'fabricContent' => 
    array (
      'name' => 'fabricContent',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Material makeup of the item.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Fabric Content',
      'group' => 'Additional Category Attributes',
      'rank' => '71000',
    ),
    'fabricCareInstructions' => 
    array (
      'name' => 'fabricCareInstructions',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Describes how the fabric should be cleaned. Enter details of the fabric care label found on the item. (For garments, typically located inside on the top of the back or the lower left side.)',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Fabric Care Instructions',
      'group' => 'Additional Category Attributes',
      'rank' => '74000',
    ),
    'isAssemblyRequired' => 
    array (
      'name' => 'isAssemblyRequired',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Is product unassembled and must be put together before use?',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Is Assembly Required',
      'group' => 'Additional Category Attributes',
      'rank' => '75000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'assemblyInstructions' => 
    array (
      'name' => 'assemblyInstructions',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Provide a URL to an image or PDF asset showing assembly instructions for items requiring assembly. URLs must be static and have no query parameters. URLs must begin with http:// or https:// and should end in in the file name.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'isAssemblyRequired',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Assembly Instructions',
      'group' => 'Additional Category Attributes',
      'rank' => '76000',
      'dataType' => 'anyURI',
    ),
    'material' => 
    array (
      'name' => 'material',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The main material(s) that a product is made of. This does not need to be an exhaustive list, but should contain the predominant or functionally important material/materials. Fabric material specifics should be entered using the "Fabric Content" attribute.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Material',
      'group' => 'Additional Category Attributes',
      'rank' => '77000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'finish' => 
    array (
      'name' => 'finish',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Terms describing the overall external treatment applied to the item. Typically finishes give a distinct appearance, texture or additional performance to the item. This attribute is used in a wide variety products and materials including wood, metal and fabric.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Finish',
      'group' => 'Additional Category Attributes',
      'rank' => '78000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'shape' => 
    array (
      'name' => 'shape',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Physical shape of the item. Used in a wide variety of products including rugs, toys and large appliances.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Shape',
      'group' => 'Additional Category Attributes',
      'rank' => '79000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'occasion' => 
    array (
      'name' => 'occasion',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The particular target time, event, or holiday for the product.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Occasion',
      'group' => 'Additional Category Attributes',
      'rank' => '80000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'sport' => 
    array (
      'name' => 'sport',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'If the product is sports-related, the name of the specific sport depicted on the product, or the target sport for the product use',
      'requiredLevel' => 'Optional',
      'displayName' => 'Sport',
      'group' => 'Additional Category Attributes',
      'rank' => '81000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'hairColorCategory' => 
    array (
      'name' => 'hairColorCategory',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Term for a basic color of human hair. Used both to describe a group of hair color dyes and to describe a component part of an item such as a doll\'s hair color. Values do not include "Brunette" because it is not a color, but describes a person with dark brown hair. Non-Natural would include colors like pink, green, and purple.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Hair Color Category',
      'group' => 'Additional Category Attributes',
      'rank' => '81500',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Blonde',
        1 => 'Brown',
        2 => 'Black',
        3 => 'Grey',
        4 => 'Silver',
        5 => 'Auburn',
        6 => 'Red',
        7 => 'White',
        8 => 'Non-Natural',
      ),
    ),
    'skinTone' => 
    array (
      'name' => 'skinTone',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Color of skin that a product is targeted for, if labeled on the product. Note: This is distinct from the color of the product. "Light Beige" may be a color, while "Fair" may be a skin tone.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Skin Tone',
      'group' => 'Additional Category Attributes',
      'rank' => '82000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'flavor' => 
    array (
      'name' => 'flavor',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The distinctive taste or flavor of the item, as provided by manufacturer. This is used for a wide variety of products, including food and beverages for both animals and humans. This may also apply to non-food items that come in flavors, including dental products, cigars and smoker wood chips.',
      'requiredLevel' => 'Recommended',
      'displayName' => 'Flavor',
      'group' => 'Additional Category Attributes',
      'rank' => '82500',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'animalType' => 
    array (
      'name' => 'animalType',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The common generic name for the type of animal.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Animal Type',
      'group' => 'Additional Category Attributes',
      'rank' => '83000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'vehicleType' => 
    array (
      'name' => 'vehicleType',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Grouping of different kinds of vehicles based on use and form. Important selection criteria, especially for compatibility, for products including boat components, tires and auto accessories. Vehicle Type also used for toys such as model rocket ships and remote-controlled race cars.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Vehicle Category',
      'group' => 'Additional Category Attributes',
      'rank' => '84000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'displayTechnology' => 
    array (
      'name' => 'displayTechnology',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The primary technology used for the item\'s display.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Display Technology',
      'group' => 'Additional Category Attributes',
      'rank' => '85000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'screenSize' => 
    array (
      'name' => 'screenSize',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Typically measured on the diagonal in inches.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Screen Size',
      'group' => 'Additional Category Attributes',
      'rank' => '86000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'in',
          ),
        ),
      ),
    ),
    'isPowered' => 
    array (
      'name' => 'isPowered',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Y indicates that an item uses electricity, requiring a power cord or batteries to operate. Useful for items that have non-powered equivalents (e.g. toothbrushes).',
      'requiredLevel' => 'Optional',
      'displayName' => 'Is Powered',
      'group' => 'Additional Category Attributes',
      'rank' => '87000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'powerType' => 
    array (
      'name' => 'powerType',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Provides information on the exact type of power used by the item.',
      'requiredLevel' => 'Conditionally Required',
      'conditionalAttributes' => 
      array (
        0 => 
        array (
          'name' => 'isPowered',
          'value' => 'Yes',
        ),
      ),
      'displayName' => 'Power Type',
      'group' => 'Additional Category Attributes',
      'rank' => '88000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'capacity' => 
    array (
      'name' => 'capacity',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'A product\'s available space. Capacity is often provided for items that contain multiple pieces of something or that can accommodate some number of objects.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Capacity',
      'group' => 'Additional Category Attributes',
      'rank' => '90000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'seatingCapacity' => 
    array (
      'name' => 'seatingCapacity',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The number of people that can be accommodated by the available seats of an item.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Seating Capacity',
      'group' => 'Additional Category Attributes',
      'rank' => '91000',
      'dataType' => 'integer',
      'totalDigits' => '7',
    ),
    'minimumWeight' => 
    array (
      'name' => 'minimumWeight',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The lower weight limit or capability of an item, often used in conjunction with "Maximum Weight". The meaning varies with context of product. For example, when used with "Maximum Weight", this attribute provides weight ranges for a range of products including pet medicine, baby carriers and outdoor play structures.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Minimum Weight',
      'group' => 'Additional Category Attributes',
      'rank' => '92000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'lb',
            1 => 'kg',
          ),
        ),
      ),
    ),
    'maximumWeight' => 
    array (
      'name' => 'maximumWeight',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The upper weight limit or capability of an item, often used in conjunction with "Minimum Weight". The meaning varies with context of product. For example, when used with "Minimum Weight", this attribute provides weight ranges for a range of products including pet medicine, baby carriers and outdoor play structures.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Maximum Weight',
      'group' => 'Additional Category Attributes',
      'rank' => '93000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'decimal',
          'totalDigits' => '16',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'lb',
            1 => 'kg',
          ),
        ),
      ),
    ),
    'maximumSpeed' => 
    array (
      'name' => 'maximumSpeed',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The maximum speed at which the item operates.	Important safety factor when applied to toys such as ride-able and remote-controlled vehicles.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Maximum Speed',
      'group' => 'Additional Category Attributes',
      'rank' => '94000',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'measure',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'integer',
          'totalDigits' => '400',
        ),
        1 => 
        array (
          'name' => 'unit',
          'maxOccurs' => '1',
          'minOccurs' => '1',
          'dataType' => 'string',
          'optionValues' => 
          array (
            0 => 'mph',
          ),
        ),
      ),
    ),
    'isTravelSize' => 
    array (
      'name' => 'isTravelSize',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Please indicate whether the product is a smaller version of a larger-sized product.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Is Travel Size',
      'group' => 'Additional Category Attributes',
      'rank' => '95000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'isInflatable' => 
    array (
      'name' => 'isInflatable',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Indicates that an item can be inflated.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Is Inflatable',
      'group' => 'Additional Category Attributes',
      'rank' => '96000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'fillMaterial' => 
    array (
      'name' => 'fillMaterial',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'The material used to stuff the item (in a cushion or plush toy, for example).',
      'requiredLevel' => 'Optional',
      'displayName' => 'Fill Material',
      'group' => 'Additional Category Attributes',
      'rank' => '97000',
    ),
    'makesNoise' => 
    array (
      'name' => 'makesNoise',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Indicates that the item has a noise-making feature. For example, a toy lawn mower that makes roaring-engine sounds.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Makes Noise',
      'group' => 'Additional Category Attributes',
      'rank' => '98000',
      'dataType' => 'string',
      'optionValues' => 
      array (
        0 => 'Yes',
        1 => 'No',
      ),
    ),
    'sportsLeague' => 
    array (
      'name' => 'sportsLeague',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'If your item has any association with a specific sports league, enter the league name. Abbreviations are fine. NOTE: This attribute flags an item for inclusion in the online fan shop.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Sports League',
      'group' => 'Additional Category Attributes',
      'rank' => '99000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'sportsTeam' => 
    array (
      'name' => 'sportsTeam',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'If your item has any association with a specific sports team, enter the team name. NOTE: This attribute flags an item for inclusion in the online fan shop.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Sports Team',
      'group' => 'Additional Category Attributes',
      'rank' => '100000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'athlete' => 
    array (
      'name' => 'athlete',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'A well-known athlete associated with a product, if applicable. This is used to group items in Fan Shop, not to describe a line of clothing.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Athlete',
      'group' => 'Nice to Have',
      'rank' => '101000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'features' => 
    array (
      'name' => 'features',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'List notable features of the item.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Additional Features',
      'group' => 'Nice to Have',
      'rank' => '107000',
    ),
    'keywords' => 
    array (
      'name' => 'keywords',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'documentation' => 'Words that people would use to search for this item. Keywords can include synonyms and related terms.',
      'requiredLevel' => 'Optional',
      'displayName' => 'Keywords',
      'group' => 'Nice to Have',
      'rank' => '108000',
      'dataType' => 'string',
      'maxLength' => '4000',
      'minLength' => '1',
    ),
    'swatchImages' => 
    array (
      'name' => 'swatchImages',
      'maxOccurs' => '1',
      'minOccurs' => '0',
      'child' => 
      array (
        0 => 
        array (
          'name' => 'swatchImage',
          'maxOccurs' => 'unbounded',
          'minOccurs' => '1',
          'child' => 
          array (
            0 => 
            array (
              'name' => 'swatchVariantAttribute',
              'maxOccurs' => '1',
              'minOccurs' => '1',
              'documentation' => 'Conditionally Required',
              'conditionalAttributes' => 
              array (
                0 => 
                array (
                  'name' => 'swatchImageUrl',
                  'value' => 'ANY',
                ),
              ),
              'displayName' => 'Swatch Variant Attribute',
              'group' => 'Item Variants',
              'dataType' => 'string',
              'optionValues' => 
              array (
                0 => 'color',
                1 => 'size',
                2 => 'countPerPack',
                3 => 'count',
                4 => 'flavor',
                5 => 'capacity',
                6 => 'shape',
                7 => 'sportsTeam',
                8 => 'character',
              ),
            ),
            1 => 
            array (
              'name' => 'swatchImageUrl',
              'maxOccurs' => '1',
              'minOccurs' => '1',
              'documentation' => 'Conditionally Required',
              'conditionalAttributes' => 
              array (
                0 => 
                array (
                  'name' => 'swatchVariantAttribute',
                  'value' => 'ANY',
                ),
              ),
              'displayName' => 'Swatch Image URL',
              'group' => 'Item Variants',
              'dataType' => 'anyURI',
            ),
          ),
        ),
      ),
    ),
  ),
);