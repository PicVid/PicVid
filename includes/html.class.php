<?php
/**
 * Klasse um HTML-Elemente verwalten zu koennen.
 *
 * @author Sebastian Brosch
 * @copyright Sebastian Brosch, 2012
 * @license http://www.gnu.org/licenses/gpl.html GPL License.
 * @since 1.0.0
 */
class HTML {
    /**
     * Methode um das Element <a> erzeugen zu koennen.
     * @param array $attributes Ein Array mit den Attributen des Elements.
     * @param string $html Ein HTML-Element welches innerhalb des <a> Elements angezeigt werden soll.
     * @return Das <a>-Element als HTML-Element.
     * @since 1.0.0
     */
    public static function a($attributes, $html = '') {

        //Objekt fuer den Link erzeugen.
        $link = new stdClass();

        //Alle Attribute des Links setzen.
        $link->class = isset($attributes['class']) ? $attributes['class'] : '';
        $link->href = isset($attributes['href']) ? $attributes['href'] : '';
        $link->id = isset($attributes['id']) ? $attributes['id'] : '';
        $link->style = isset($attributes['style']) ? $attributes['style'] : '';
        $link->tabindex = isset($attributes['tabindex']) ? $attributes['tabindex'] : '';
        $link->target = isset($attributes['target']) ? $attributes['target'] : '';
        $link->text = isset($attributes['text']) ? $attributes['text'] : '';
        $link->title = isset($attributes['title']) ? $attributes['title'] : '';

        //Werte pruefen und volle Attribute setzen.
        $link->class = (trim($link->class) !== '') ? ' class="'.$link->class.'"' : '';
        $link->href = (trim($link->href) !== '') ? ' href="'.$link->href.'"' : '';
        $link->id = (trim($link->id) !== '') ? ' id="'.$link->id.'"' : '';
        $link->style = (trim($link->style) !== '') ? ' style="'.$link->style.'"' : '';
        $link->tabindex = (trim($link->tabindex) !== '') ? ' tabindex="'.$link->tabindex.'"' : '';
        $link->target = (trim($link->target) !== '') ? ' target="'.$link->target.'"' : '';
        $link->text = (trim($html) !== '') ? $html : $link->text;
        $link->title = (trim($link->title)) ? ' title="'.$link->title.'"' : '';

        //Element als Zeichenkette erzeugen.
        $element = '<a'.$link->class.$link->href.$link->id.$link->style.$link->tabindex.$link->target.$link->title.'>'.$link->text.'</a>';

        //Element zurueckgeben.
        return $element;
    }

    /**
     * Methode um das Element <img> erzeugen zu koennen.
     * @param array $attributes Ein Array mit den Attributen des Elements.
     * @return Das <img>-Element asl HTML-Element.
     * @since 1.0.0
     */
    public static function img($attributes) {

        //Objekt fuer das Bild erzeugen.
        $image = new stdClass();

        //Alle Eigenschaften des Bildes setzen.
        $image->alt = isset($attributes['alt']) ? $attributes['alt'] : '';
        $image->class = isset($attributes['class']) ? $attributes['class'] : '';
        $image->height = isset($attributes['height']) ? $attributes['height'] : 0;
        $image->id = isset($attributes['id']) ? $attributes['id'] : '';
        $image->src = isset($attributes['src']) ? $attributes['src'] : '';
        $image->style = isset($attributes['style']) ? $attributes['style'] : '';
        $image->title = isset($attributes['title']) ? $attributes['title'] : '';
        $image->width = isset($attributes['width']) ? $attributes['width'] : 0;

        //Werte pruefen und volle Attribute erzeugen.
        $image->alt = (trim($image->alt) !== '') ? ' alt="'.$image->alt.'"' : '';
        $image->class = (trim($image->class) !== '') ? ' class="'.$image->class.'"' : '';
        $image->height = ($image->height !== 0) ? ' height="'.$image->height.'"' : '';
        $image->id = (trim($image->id) !== '') ? ' id="'.$image->id.'"' : '';
        $image->src = (trim($image->src) !== '') ? ' src="'.$image->src.'"' : '';
        $image->style = (trim($image->style) !== '') ? ' style="'.$image->style.'"' : '';
        $image->title = (trim($image->title)) ? ' title="'.$image->title.'"' : '';
        $image->width = ($image->width !== 0) ? ' width="'.$image->width.'"' : '';

        //Element als Zeichenkette erzeugen.
        $element = '<img'.$image->alt.$image->class.$image->height.$image->id.$image->src.$image->style.$image->title.$image->width.'/>';

        //Element zurueckgeben.
        return $element;
    }

    /**
     * Methode um das Element <input> erzeugen zu koennen.
     * @param array $attributes Ein Array mit den Attributen des Elements.
     * @return Die Option als HTML-Element.
     * @since 1.0.0
     */
    public static function input($attributes) {

        //Neues Standardobjekt erzeugen.
        $input = new stdClass();

        //Attribute in das Objekt schreiben.
        $input->checked = isset($attributes['checked']) ? $attributes['checked'] : false;
        $input->class = isset($attributes['class']) ? $attributes['class'] : '';
        $input->id = isset($attributes['id']) ? $attributes['id'] : '';
        $input->maxlength = isset($attributes['maxlength']) ? $attributes['maxlength'] : '';
        $input->name = isset($attributes['name']) ? $attributes['name'] : '';
        $input->readonly = isset($attributes['readonly']) ? $attributes['readonly'] : false;
        $input->type = isset($attributes['type']) ? $attributes['type'] : 'text';
        $input->value = isset($attributes['value']) ? $attributes['value'] : '';

        //Werte pruefen und volle Attribute erzeugen.
        $input->checked = ($input->checked !== false) ? ' checked="checked"' : '';
        $input->class = (trim($input->class) !== '') ? ' class="'.$input->class.'"' : '';
        $input->id = (trim($input->id) !== '') ? ' id="'.$input->id.'"' : '';
        $input->maxlength = (trim($input->maxlength) !== '') ? ' maxlength="'.(int) $input->maxlength.'"' : '';
        $input->name = (trim($input->name) !== '') ? ' name="'.$input->name.'"' : '';
        $input->readonly = ($input->readonly !== false) ? 'readonly' : '';
        $input->type = (trim($input->type) !== '') ? ' type="'.$input->type.'"' : '';
        $input->value = (trim($input->value) !== '') ? ' value="'.$input->value.'"' : '';

        //Element als Zeichenkette erzeugen.
        $element = '<input'.$input->checked.$input->class.$input->id.$input->maxlength.$input->name.$input->readonly.$input->type.$input->value.'/>';

        //Element zurueckgeben.
        return $element;
    }

    /**
     * Methode um das Element <option> erzeugen zu koennen.
     * @param array $attributes Ein Array mit den Attributen des Elements.
     * @return Die Option als HTML-Element.
     * @since 1.0.0
     */
    public static function option($attributes) {

        //Neues Standardobjekt erzeugen.
        $option = new stdClass();

        //Attribute in das Option-Objekt schreiben.
        $option->class = isset($attributes['class']) ? $attributes['class'] : '';
        $option->disabled = isset($attributes['disabled']) ? $attributes['disabled'] : false;
        $option->id = isset($attributes['id']) ? $attributes['id'] : '';
        $option->selected = isset($attributes['selected']) ? $attributes['selected'] : false;
        $option->style = isset($attributes['style']) ? $attributes['style'] : '';
        $option->text = isset($attributes['text']) ? $attributes['text'] : '';
        $option->value = isset($attributes['value']) ? $attributes['value'] : '';

        //Werte pruefen und volle Attribute erzeugen.
        $option->class = (trim($option->class) !== '') ? ' class="'.$option->class.'"' : '';
        $option->disabled = ($option->disabled === true) ? ' disabled="disabled"' : '';
        $option->id = (trim($option->id) !== '') ? ' id="'.$option->id.'"' : '';
        $option->selected = ($option->selected === true) ? ' selected="selected"' : '';
        $option->style = (trim($option->style) !== '') ? ' style="'.$option->style.'"' : '';
        $option->value = (trim($option->value) !== '') ? ' value="'.$option->value.'"' : '';

        //Element als Zeichenkette erzeugen.
        $element = '<option'.$option->class.$option->disabled.$option->id.$option->selected.$option->style.$option->value.'>'.$option->text.'</option>';

        //Element zurueckgeben.
        return $element;
    }

    /**
     * Methode um das Element <select> erzeugen zu koennen.
     * @param array $attributes Ein Array mit den Attributen der Select-List.
     * @param array $options Ein Array mit allen Optionen der Select-List.
     * @return Die Select-List als HTML-Element.
     * @since 1.0.0
     */
    public static function select($attributes, $options) {

        //Neues Standardobjekt erzeugen.
        $select = new stdClass();

        //Attribute in das Select-Objekt schreiben.
        $select->class = isset($attributes['class']) ? $attributes['class'] : '';
        $select->disabled = isset($attributes['disabled']) ? $attributes['disabled'] : false;
        $select->id = isset($attributes['id']) ? $attributes['id'] : '';
        $select->name = isset($attributes['name']) ? $attributes['name'] : '';
        $select->size = isset($attributes['size']) ? $attributes['size'] : 1;
        $select->script = isset($attributes['script']) ? $attributes['script'] : '';
        $select->selected = isset($attributes['selected']) ? $attributes['selected'] : '';
        $select->style = isset($attributes['style']) ? $attributes['style'] : '';
        $select->onchange = isset($attributes['onchange']) ? $attributes['onchange'] : '';
        $select->onkeyup = isset($attributes['onkeyup']) ? $attributes['onkeyup'] : '';

        //Werte pruefen und volle Attribute erzeugen.
        $select->class = (trim($select->class) !== '') ? ' class="'.$select->class.'"' : '';
        $select->disabled = ($select->disabled === true) ? ' disabled="disabled"' : '';
        $select->id = (trim($select->id) !== '') ? ' id="'.$select->id.'"' : '';
        $select->name = (trim($select->name) !== '') ? ' name="'.$select->name.'"' : '';
        $select->size = ' size="'.$select->size.'"';
        $select->script = (trim($select->script) !== '') ? ' '.$select->script : '';
        $select->style = (trim($select->style) !== '') ? ' style="'.$select->style.'"' : '';
        $select->onchange = (trim($select->onchange) !== '') ? ' onchange="'.$select->onchange.'"' : '';
        $select->onkeyup = (trim($select->onkeyup) !== '') ? ' onchange="'.$select->onkeyup.'"' : '';

        //Element als Zeichenkette erzeugen.
        $element = '<select'.$select->class.$select->disabled.$select->id.$select->name.$select->size.$select->style.$select->script.$select->onchange.$select->onkeyup.'>';

        //Optionen in die Select-List einfuegen.
        if(count($options) > 0) {

            //Durchlaufen aller <option>-Elemente.
            foreach($options as $option) {

                //Pruefen ob ein bestimmter Wert ausgewaehlt werden soll.
                if($select->selected !== '') {

                    //Pruefen ob die aktuelle <option> gewaehlt werden soll.
                    if(strpos($option, 'value="'.$select->selected.'"', 0) !== false) {

                        //<option>-Element veraendern und auswaehlen.
                        $option = str_replace('value="'.$select->selected.'"', 'value="'.$select->selected.'" selected="selected"', $option);

                        //Standardwert zuruecksetzen.
                        $select->selected = '';
                    }
                }

                //<option>-Element einfuegen.
                $element .= "\n\t".$option;
            }
        }

        //Ende der Select-List erzeugen.
        $element .= "\n".'</select>';

        //Select-List zurueckgeben.
        return $element;
    }
}
?>