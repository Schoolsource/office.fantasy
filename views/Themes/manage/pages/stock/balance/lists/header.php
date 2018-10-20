<div ref="header" class="listpage2-header clearfix">

    <div ref="actions" class="listpage2-actions">
        <div class="clearfix mbs mtm">

            <ul class="lfloat" ref="actions">
                <li>
                    <h2><i class="icon-balance-scale"></i><span class="mls">Stock Balance</span></h2>
                </li>

                <li><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text' => 'refresh')); ?>"><i class="icon-refresh"></i></a></li>

                <!-- <li class="divider"></li> -->

                 <!-- data-plugins="dialog" -->
                <!-- <li class="mt">
                    <a href="<?=URL; ?>orders/import" class="btn btn-blue" data-plugins="dialog"><i class="icon-plus"></i> IMPORT ORDER</a>
                </li> -->

            </ul>

            <ul class="lfloat selection hidden_elem" ref="selection">
                <li><span class="count-value"></span></li>
                <li><a class="btn-icon"><i class="icon-download"></i></a></li>
                <li><a class="btn-icon"><i class="icon-trash"></i></a></li>
            </ul>


            <ul class="rfloat" ref="control">
                <li><button type="button" class="btn btn-blue" ref="form" data-url="<?=URL; ?>pdf/stockBalance"><i class="icon-file-pdf-o"></i><span class="mls">Export PDF</span></button></li>
            </ul>

        </div>
        <div class="clearfix mbl mtm">
            <ul class="lfloat" ref="control">
                <li>
                    <!-- <label for="closedate" class="label">Choose date</label><select class="inputtext" plugin="closedate2">
                        <option value=""> -- All --</option>
                        <option value="">This Month</option>
                        <option value="custom">Custom</option>
                    </select> -->
                    <label for="closedate" class="label">Choose date</label><select ref="closedate" name="closedate" class="inputtext">
                        <option value="daily">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="weekly">This week</option>
                        <option value="monthly">This month</option>
                        <option value="custom">Custom</option>
                        <option value="latest" selected>Real Time</option>
                    </select>
                </li>

                <li class="divider" style="height: 45px;margin: 0;"></li>

                <li>
                    <label class="label" for="category">Product Category:</label>
                    <select ref="selector" id="category" name="category" class="inputtext">
                        <option value=""> -- All --</option>
                        <?php
                        foreach ($this->categoryLists as $key => $value) {
                            echo '<option value="'.$value['id'].'">'.$value['name_en'].'</option>';
                        }
                        ?>
                    </select>
                </li>

                <li>
                    <label class="label" for="pds_has_vat">VAT:</label>
                    <select ref="selector" id="pds_has_vat" name="pds_has_vat" class="inputtext">
                        <option value=""> -- All --</option>
                        <option value="1">VAT Only</option>

                    </select>
                </li>

                <!-- <li><label class="checkbox" style="line-height: 20px;font-weight: bold;margin-top: 18px;"><input type="checkbox" name="pds_has_vat" data-action="check"><span>VAT Only</span></label></li> -->

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