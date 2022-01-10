<?php

/**
 * create an input field contained by a div.
 * @param string $type input type attribute
 * @param string $label label visual text.
 * @param string $name input id, name and for label attribute.
 * @param array $inputAttrs any attributes you want to add to input field.
 * @param array $divAttrs any attributes you want to add to div container.
 * @return string HTML Tags
 */
function inputBlock($type, $label, $name, $value = '', $inputAttrs = [], $divAttrs = [])
{
  $inputString = stringifyAttrs($inputAttrs);
  $divString = stringifyAttrs($divAttrs);
  $html = "<div $divString>";
  $html .= "<label for=" . $name . ">$label</label>";
  $html .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . $value . '"' . $inputString . '/>';
  $html .= '</div>';
  return $html;
}

/**
 * create a submit input field.
 * @param string $buttonText value attribute.
 * @param array $inputAttrs any attributes you want to add to input field.
 * @return string HTML Tags
 */
function submitButton($buttonText, $inputAttrs = [])
{
  $inputString = stringifyAttrs($inputAttrs);
  $html = '<input type="submit" value="' . $buttonText . '"' . $inputString . ' />';
  return $html;
}

/**
 * create a submit input field contained by a div.
 * @param string $buttonText value attribute.
 * @param array $inputAttrs any attributes you want to add to input field.
 * @param array $divAttrs any attributes you want to add to div container.
 * @return string HTML Tags
 */
function submitBlock($buttonText, $inputAttrs = [], $divAttrs = [])
{
  $inputString = stringifyAttrs($inputAttrs);
  $divString = stringifyAttrs($divAttrs);
  $html = '<div' . $divString . '>';
  $html .= '<input type="submit" value="' . $buttonText . '"' . $inputString . ' />';
  $html .= '</div>';
  return $html;
}

/**
 * convert attributes array to string of attributes.
 * @param array $attrs
 * @return string
 */
function stringifyAttrs($attrs)
{
  $string = '';
  foreach ($attrs as $key => $val) {
    $string .= ' ' . $key . '="' . $val . '"'; // e.g. class="container"
  }
  return $string;
}
