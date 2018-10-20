<div ref="header" class="listpage2-header clearfix">

    <div ref="actions" class="listpage2-actions">
        <div class="clearfix mbs mtm">

            <ul class="lfloat" ref="actions">
                <li class="mt">
                    <h2><i class="icon-diamond mrs"></i><span> VAT BUY</span></h2>
                </li>

                <li class="mt"><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text' => 'refresh')); ?>"><i class="icon-refresh"></i></a></li>

                <li class="divider"></li>

                 <!-- data-plugins="dialog" -->
                <li class="mt">
                    <a data-plugins="dialog" href="<?=URL; ?>tax/add" class="btn btn-blue"><i class="icon-plus"></i> <?=$this->lang->translate('Add New'); ?></a>
                </li>

            </ul>

            <ul class="lfloat selection hidden_elem" ref="selection">
                <li><span class="count-value"></span></li>
                <li><a class="btn-icon"><i class="icon-download"></i></a></li>
                <li><a class="btn-icon"><i class="icon-trash"></i></a></li>
            </ul>


            <ul class="rfloat" ref="control">
                <li><label class="fwb fcg fsm" for="limit">Show</label>
                <select ref="selector" id="limit" name="limit" class="inputtext"><?php
                    echo '<option value="20">20</option>';
                    echo '<option selected value="50">50</option>';
                    echo '<option value="100">100</option>';
                    echo '<option value="200">200</option>';
                ?></select><span id="more-link">Loading...</span></li>
            </ul>

        </div>
        <div class="clearfix mbl mtm">
            <ul class="lfloat" ref="control">
                <li>
                    <label for="closedate" class="label">Choose date</label><select ref="closedate" name="closedate" class="inputtext">
                        <option value="daily">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="weekly">This week</option>
                        <option value="monthly" selected>This month</option>
                        <option value="custom">Custom</option>
                    </select>
                    <!-- <input ref="date" name="date"> -->
                </li>
                <li>
                    <label for="credit" class="label">Credit</label>
                    <select ref="selector" name="credit" class="inputtext">
                        <option value="">-</option>
                        <?php
                        foreach ($this->credit as $key => $value) {
                            echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <label for="credit" class="label">Category</label>
                    <select ref="selector" name="category" class="inputtext">
                        <option value="">-</option>
                        <?php
                        foreach ($this->category as $key => $value) {
                            echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
                        }
                        ?>
                    </select>
                </li>
                <!-- <li>
                    <label for="print" class="label">Print</label>
                    <a href="<?=URL; ?>tax/plate" data-plugins="dialog" class="btn btn-red"><i class="icon-file-pdf-o"></i> PDF</a>
                </li> -->
            </ul>
            <ul class="rfloat" ref="control">
                <li class="mt"><form class="form-search" action="#">
                    <input class="inputtext search-input" type="text" id="search-query" placeholder="<?=$this->lang->translate('Search'); ?>" name="q" autocomplete="off">
                    <span class="search-icon">
                        <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
                    </span>

                </form></li>
            </ul>
        </div>

    </div>

</div>
