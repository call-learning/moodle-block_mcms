@block @block_mcms
Feature: Adding and configuring MCMS block
  In order to have the MCMS block used
  As a admin
  I need to add the MCMS block to the front page

  @javascript @_file_upload
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
      | Title | MCMS Test |
      | Content | A typical content <p> With a paragraph </p> |
      | Additional CSS classes | a-class-test |
      | Decoration styles for this block | a-decoration |
    And I click on "#id_config_layout_layout_one" "css_element"
    And I upload "blocks/mcms/tests/fixtures/icon.svg" file to "Image" filemanager
      | Save as | icon.svg |
    And I press "Save changes"
    Then "//*[contains(@class, 'block-mcms')]//div[contains(@class, 'blockicon')][contains(@style, 'icon.svg')]" "xpath_element" should exist
    And I should see "A typical content"
    And I should see "MCMS Test"

  @javascript @_file_upload
  Scenario: Adding MCMS block with Layout 2 and I add a background image
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title | MCMS Test |
      | Content | A typical content <p> With a paragraph </p> |
      | Additional CSS classes | a-class-test |
      | Decoration styles for this block | a-decoration |
    And I click on "#id_config_layout_layout_two" "css_element"
    And I upload "blocks/mcms/tests/fixtures/background.jpg" file to "Image" filemanager
      | Save as | background.jpg |
    And I press "Save changes"
    Then "//*[contains(@class, 'block-mcms')][contains(@style, 'background.jpg')]" "xpath_element" should exist

  @javascript @_file_upload
  Scenario: Adding MCMS block with Layout 3 and I add a side image, then the image should be displayed
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title | MCMS Test |
      | Content | A typical content <p> With a paragraph </p> |
      | Additional CSS classes | a-class-test |
      | Decoration styles for this block | a-decoration |
    And I click on "#id_config_layout_layout_three" "css_element"
    And I upload "blocks/mcms/tests/fixtures/text-image.jpg" file to "Image" filemanager
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should exist
    And the image at "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should be identical to "blocks/mcms/tests/fixture/text-image.jpg"

  @javascript @_file_upload
  Scenario: Adding MCMS block with Layout 4 and I add a side image, then the image should be displayed
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title | MCMS Test |
      | Content | A typical content <p> With a paragraph </p> |
      | Additional CSS classes | a-class-test |
      | Decoration styles for this block | a-decoration |
    And I click on "#id_config_layout_layout_four" "css_element"
    And I upload "blocks/mcms/tests/fixtures/text-image.jpg" file to "Image" filemanager
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should exist
    And the image at "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should be identical to "blocks/mcms/tests/fixture/text-image.jpg"

  @javascript @_file_upload
  Scenario: Adding MCMS block with Layout 3 and I add a side image, then upload a new image, the new image should be displayed
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "MCMS" block
    And I configure the "MCMS" block
    Given I set the following fields to these values:
      | Title | MCMS Test |
      | Content | A typical content <p> With a paragraph </p> |
      | Additional CSS classes | a-class-test |
      | Decoration styles for this block | a-decoration |
    And I click on "#id_config_layout_layout_three" "css_element"
    And I upload "blocks/mcms/tests/fixtures/text-image.jpg" file to "Image" filemanager
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should exist
    And the image at "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should be identical to "blocks/mcms/tests/fixture/text-image.jpg"
    And I configure the "MCMS Test" block
    And I delete "icon.svg" from "MCMS image" filemanager
    And I upload "blocks/mcms/tests/fixtures/other-image.jpg" file to "Image" filemanager
      | Save as | side-image.jpg |
    And I press "Save changes"
    Then "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should exist
    And the image at "//*[contains(@class, 'block-mcms')]//img[contains(@src, 'side-image.jpg')]" "xpath_element" should be identical to "blocks/mcms/tests/fixture/other-image.jpg"
