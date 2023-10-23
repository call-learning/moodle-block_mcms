@block @block_mcms @_file_upload @javascript
Feature: Adding and configuring MCMS block
  In order to have the MCMS block used
  As a admin
  I need to add the MCMS block to the front page

  Scenario: Adding MCMS block with Layout 1 and I add an icon, this should result in the new icon being displayed.
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Then I should see "Title"
    Then I should see "Content"
    Then I should see "Image"
    Then I should see "Additional CSS classes"
    Then I should see "Decoration styles for this block"
    Then I should see "Layout"
    Given I set the following fields to these values:
      | Title                            | MCMS Test                                   |
      | Content                          | A typical content <p> With a paragraph </p> |
      | Additional CSS classes           | a-class-test                                |
      | Decoration styles for this block | a-decoration                                |
    And I click on "#id_config_layout_layout_one" "css_element"
    And I upload "blocks/mcms/tests/fixtures/icon.svg" file to "Image" filemanager as:
      | Save as | icon.svg |
    And I press "Save changes"
    Then "icon.svg" "block_mcms > Block MCMS Images" should exist
    And I should see "A typical content"
    And I should see "MCMS Test"

  Scenario: Adding MCMS block with Layout 2 and I add a background image
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title                            | MCMS Test                                   |
      | Content                          | A typical content <p> With a paragraph </p> |
      | Additional CSS classes           | a-class-test                                |
      | Decoration styles for this block | a-decoration                                |
    And I click on "#id_config_layout_layout_two" "css_element"
    And I upload "blocks/mcms/tests/fixtures/background.jpg" file to "Image" filemanager as:
      | Save as | background.jpg |
    And I press "Save changes"
    Then "background.jpg" "block_mcms > Block MCMS Images" should exist

  Scenario: Adding MCMS block with Layout 3 and I add a side image, then the image should be displayed
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title                            | MCMS Test                                   |
      | Content                          | A typical content <p> With a paragraph </p> |
      | Additional CSS classes           | a-class-test                                |
      | Decoration styles for this block | a-decoration                                |
    And I click on "#id_config_layout_layout_three" "css_element"
    And I upload "blocks/mcms/tests/fixtures/text-image.jpg" file to "Image" filemanager as:
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "side-image.jpg" "block_mcms > Block MCMS Images" should exist
    And the image at "side-image.jpg" "block_mcms > Block MCMS Images" should be identical to "blocks/mcms/tests/fixtures/text-image.jpg"

  Scenario: Adding MCMS block with Layout 4 and I add a side image, then the image should be displayed
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title                            | MCMS Test                                   |
      | Content                          | A typical content <p> With a paragraph </p> |
      | Additional CSS classes           | a-class-test                                |
      | Decoration styles for this block | a-decoration                                |
    And I click on "#id_config_layout_layout_four" "css_element"
    And I upload "blocks/mcms/tests/fixtures/text-image.jpg" file to "Image" filemanager as:
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "side-image.jpg" "block_mcms > Block MCMS Images" should exist
    And the image at "side-image.jpg" "block_mcms > Block MCMS Images" should be identical to "blocks/mcms/tests/fixtures/text-image.jpg"

  Scenario: Adding MCMS block with Layout 3 and I add a side image, then upload a new image, the new image should be displayed
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title                            | MCMS Test                                   |
      | Content                          | A typical content <p> With a paragraph </p> |
      | Additional CSS classes           | a-class-test                                |
      | Decoration styles for this block | a-decoration                                |
    And I click on "#id_config_layout_layout_three" "css_element"
    And I upload "blocks/mcms/tests/fixtures/text-image.jpg" file to "Image" filemanager as:
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "side-image.jpg" "block_mcms > Block MCMS Images" should exist
    And the image at "side-image.jpg" "block_mcms > Block MCMS Images" should be identical to "blocks/mcms/tests/fixtures/text-image.jpg"
    And I configure the "MCMS Test" block
    And I delete "side-image.jpg" from "Image" filemanager
    And I upload "blocks/mcms/tests/fixtures/other-image.jpg" file to "Image" filemanager as:
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "side-image.jpg" "block_mcms > Block MCMS Images" should exist
    And the image at "side-image.jpg" "block_mcms > Block MCMS Images" should be identical to "blocks/mcms/tests/fixtures/other-image.jpg"
