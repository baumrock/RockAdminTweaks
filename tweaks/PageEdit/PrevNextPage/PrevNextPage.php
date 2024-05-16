<?php

namespace RockAdminTweaks;

use ProcessWire\RepeaterPage;

class PrevNextPage extends Tweak
{
    private $editedPage;

    public function info(): array
    {
        return [
            'description' => "Add buttons/options to edit prev/next page",
        ];
    }


    public function ready(): void
    {
        if ($this->wire()->config->ajax) {
            return;
        }

        $this->editedPage = false;
        $editedPageId = $this->wire('sanitizer')->int($this->wire('input')->get->id);
        $editedPage = $this->wire('pages')->get($editedPageId);

        if ($editedPage->id && !($editedPage instanceof RepeaterPage)) {
            $this->editedPage = $editedPage;
        }

        if (
            in_array($this->wire('page')->process, ['ProcessPageEdit', 'ProcessUser', 'ProcessRole'])
            && $this->wire('input')->id
            && $this->editedPage
        ) {
            // sort precedence: template level - page level - "sort"
            $sortfield = 'sort';
            $parent = $this->editedPage->parent();

            if ($parent->id) {
                $sortfield = $parent->template->sortfield ?: $parent->sortfield;
            }

            $p404_id = $this->wire('config')->http404PageID;
            $baseSelector = "include=all, template!=admin, id!=$p404_id, parent=$parent";
            $prevnextlinks = array();
            $isFirst = false;
            $isLast = false;
            $numSiblings = $parent->numChildren(true);

            if ($numSiblings > 1) {
                $selector = $baseSelector . ', sort=' . $sortfield;

                if (strpos($sortfield, '-') === 0) {
                    $sortfieldReversed = ltrim($sortfield, '-');
                } else {
                    $sortfieldReversed = '-' . $sortfield;
                }

                $next = $this->editedPage->next($selector);
                $prev = $this->editedPage->prev($selector);

                if (!$next->id) {
                    $next = $this->editedPage->siblings($selector . ', limit=1')->first();
                    $isFirst = true;
                }

                if (!$prev->id) {
                    $prev = $this->editedPage->siblings("$baseSelector, limit=1, sort=" . $sortfieldReversed)->first();
                    $isLast = true;
                }

                $edit_next_text = $isFirst ? ' ' . $this->_('Edit first:') : $this->_('Edit next:');
                $edit_prev_text = $isLast  ? ' ' . $this->_('Edit last:')  : $this->_('Edit previous:');

                if ($prev && $prev->id && $prev->editable()) {
                    $prevnextlinks['prev'] = array(
                        'title' => $edit_prev_text . ' ' . ($prev->title ? $prev->title : $prev->name),
                        'url' => $prev->editUrl,
                    );
                }

                if ($next && $next->id && $next->editable()) {
                    $prevnextlinks['next'] = array(
                        'title' => $edit_next_text . ' ' . ($next->title ? $next->title : $next->name),
                        'url' => $next->editUrl,
                    );
                }

                if (!empty($prevnextlinks)) {
                    $this->wire('config')->js('AOS_prevnextlinks', $prevnextlinks);
                    $this->loadJS();
                    $this->loadCSS();
                }
            }
        }
    }
}
