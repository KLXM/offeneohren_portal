<?php

/**
 * Link-Checker
 * Checks URL and URL-Chat of services for HTTP 200 via an internal AJAX ping.
 */

$addon = rex_addon::get('offeneohren_portal');

// 1) AJAX Action: Check single URL
$ajaxCheck = rex_request('ajax_check', 'string', '');

if ($ajaxCheck === '1') {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $url = rex_request('url', 'string', '');
    
    if ($url === '') {
        rex_response::sendJson(['status' => 'empty', 'code' => 0]);
        exit;
    }

    try {
        // Prepare cURL to follow redirects and get the final response code
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // Versuche zuerst HEAD
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8); // 8 seconds timeout
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Manche Server/Firewalls (zB Cloudflare) verweigern HEAD-Requests mit 403, 405 etc. 
        // Falls wir sowas kassieren, probieren wir sicherheitshalber noch einen echten GET-Request
        if ($httpCode == 403 || $httpCode == 405 || $httpCode == 400 || $httpCode == 406) {
            curl_setopt($ch, CURLOPT_NOBODY, false);
            // Um keine riesigen Dateien runterzuladen, brechen wir nach ein paar Bytes per Callback ab
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) {
                // Wir tun einfach mal so, als hätten wir genug gelesen
                return -1; // -1 cancelt den Download sofort (liefert CURLE_WRITE_ERROR)
            });
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // Wir ignorieren den CURLE_WRITE_ERROR, uns interessiert nur der Code!
            // Aber Achtung: Wenn Cloudflare bei GET trotzdem 403 wirft, bleibt es 403.
        }
        
        if (curl_errno($ch) && curl_errno($ch) !== CURLE_WRITE_ERROR) {
            $httpCode = 0; // Tatsächlicher Fehler (Network, Timeout)
        }
        
        curl_close($ch);
        
        // Status Evaluierung
        if ($httpCode >= 200 && $httpCode < 400) {
            $status = 'ok';
        } elseif ($httpCode == 403) {
            // 403 ist oft Cloudflare/WAF Bot-Protection. Der Server ist also da und "funktioniert".
            $status = 'warning_403';
        } elseif ($httpCode == 405) {
            $status = 'warning_405';
        } else {
            $status = 'error';
        }
        
        rex_response::sendJson(['status' => $status, 'code' => $httpCode]);
    } catch (Exception $e) {
        rex_response::sendJson(['status' => 'error', 'code' => 500]);
    }
    
    exit;
}

// 2) AJAX Action: Delete Dataset
$ajaxDelete = rex_request('ajax_delete', 'string', '');

if ($ajaxDelete === '1') {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $id = rex_request('id', 'int', 0);
    $table = rex_request('table', 'string', 'rex_yf_service');
    
    if ($table !== 'rex_yf_service' && $table !== 'rex_yf_alternate') {
        $table = 'rex_yf_service';
    }
    
    if ($id > 0) {
        $sql = rex_sql::factory();
        // Option 1: Direct SQL delete for safety if YORM model classes are missing
        $sql->setQuery('DELETE FROM ' . $table . ' WHERE id = ?', [$id]);
        rex_response::sendJson(['status' => 'ok']);
    } else {
        rex_response::sendJson(['status' => 'error']);
    }
    exit;
}

$sql = rex_sql::factory();
// We only check services that are either online (1) or in review (2)
$query = "SELECT id, name, url, url_chat FROM rex_yf_service WHERE status IN (1, 2) AND (url != '' OR url_chat != '') ORDER BY id DESC";
$services = $sql->getArray($query);

$_csrf_key = 'table_field-rex_yf_service';
$_csrf_params = rex_csrf_token::factory($_csrf_key)->getUrlParams();

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Tote Website- & Chat-Links finden</h3>
    </div>
    <div class="panel-body">
        <p>Überprüfe die hinterlegten Webseiten- und Chat-URLs der Einrichtungen auf Erreichbarkeit (Status 200 / 300).</p>
        
        <div class="alert alert-info">
            <b>Legende & Hinweise:</b><br>
            <ul style="margin-bottom: 0;">
                <li><span class="label label-success"><i class="fa fa-check"></i> OK</span>: Der Link ist fehlerfrei erreichbar.</li>
                <li><span class="label label-warning"><i class="rex-icon rex-icon-warning"></i> Bot-Schutz / 403</span>: Der Server verbietet automatische Prüfungen durch diesen Scanner (z. B. durch Cloudflare). <b>Bitte klicke selbst auf den Link, prüfe ihn im Browser und markiere ihn bei Erfolg über das grüne Häkchen in der "Aktionen"-Spalte als Manuell OK.</b></li>
                <li><span class="label label-danger"><i class="rex-icon rex-icon-error"></i> Fehler / Timeout</span>: Die Website ist entweder komplett offline, fehlerhaft (z. B. 404) oder nicht mehr registriert. Hier solltest du den Datensatz reparieren oder löschen.</li>
            </ul>
        </div>
        
        <button id="oo-start-check" class="btn btn-primary"><i class="rex-icon rex-icon-refresh"></i> Link-Check starten / fortsetzen</button>
        <button id="oo-stop-check" class="btn btn-default" style="display:none;">Pausieren</button>
        <button id="oo-reset-check" class="btn btn-warning pull-right"><i class="rex-icon rex-icon-delete"></i> Scan zurücksetzen</button>
        <hr>
        
        <div id="oo-check-progress" style="display:none;" class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%;">
                0%
            </div>
        </div>

        <table class="table table-striped table-hover" id="oo-link-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Einrichtung</th>
                    <th>Typ</th>
                    <th>URL</th>
                    <th width="150">Status</th>
                    <th width="100">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <?php if ($service['url'] !== ''): ?>
                        <tr class="oo-link-row" data-url="<?= rex_escape($service['url']) ?>" data-id="<?= $service['id'] ?>" data-table="rex_yf_service">
                            <td><?= $service['id'] ?></td>
                            <td><a href="<?= rex_url::backendPage('yform/manager/data_edit', ['page' => 'yform/manager/data_edit', 'table_name' => 'rex_yf_service', 'data_id' => $service['id'], 'func' => 'edit', '_csrf_token' => $_csrf_params['_csrf_token']]) ?>" target="_blank"><?= rex_escape($service['name']) ?></a></td>
                            <td><span class="label label-info">Website</span></td>
                            <td><a href="<?= rex_escape($service['url']) ?>" target="_blank" style="word-break: break-all;"><?= rex_escape($service['url']) ?></a></td>
                            <td class="oo-status-cell"><span class="text-muted"><i class="rex-icon rex-icon-question"></i> Ausstehend</span></td>
                            <td style="white-space: nowrap;">
                                <button type="button" class="btn btn-default btn-xs oo-rescan-btn" title="Erneut prüfen" style="margin-right:2px;"><i class="rex-icon rex-icon-refresh"></i></button>
                                <button type="button" class="btn btn-success btn-xs oo-manual-ok-btn" title="Manuell als OK markieren" style="margin-right:2px;"><i class="fa fa-check"></i></button>
                                <button type="button" class="btn btn-danger btn-xs oo-delete-btn" title="Datensatz löschen"><i class="rex-icon rex-icon-delete"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($service['url_chat'] !== ''): ?>
                        <tr class="oo-link-row" data-url="<?= rex_escape($service['url_chat']) ?>" data-id="<?= $service['id'] ?>" data-table="rex_yf_service">
                            <td><?= $service['id'] ?></td>
                            <td><a href="<?= rex_url::backendPage('yform/manager/data_edit', ['page' => 'yform/manager/data_edit', 'table_name' => 'rex_yf_service', 'data_id' => $service['id'], 'func' => 'edit', '_csrf_token' => $_csrf_params['_csrf_token']]) ?>" target="_blank"><?= rex_escape($service['name']) ?></a></td>
                            <td><span class="label label-success">Chat</span></td>
                            <td><a href="<?= rex_escape($service['url_chat']) ?>" target="_blank" style="word-break: break-all;"><?= rex_escape($service['url_chat']) ?></a></td>
                            <td class="oo-status-cell"><span class="text-muted"><i class="rex-icon rex-icon-question"></i> Ausstehend</span></td>
                            <td style="white-space: nowrap;">
                                <button type="button" class="btn btn-default btn-xs oo-rescan-btn" title="Erneut prüfen" style="margin-right:2px;"><i class="rex-icon rex-icon-refresh"></i></button>
                                <button type="button" class="btn btn-success btn-xs oo-manual-ok-btn" title="Manuell als OK markieren" style="margin-right:2px;"><i class="fa fa-check"></i></button>
                                <button type="button" class="btn btn-danger btn-xs oo-delete-btn" title="Datensatz löschen"><i class="rex-icon rex-icon-delete"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnStart = document.getElementById('oo-start-check');
        const btnStop = document.getElementById('oo-stop-check');
        const btnReset = document.getElementById('oo-reset-check');
        const progressContainer = document.getElementById('oo-check-progress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        
        let rows = Array.from(document.querySelectorAll('.oo-link-row'));
        let currentIndex = 0;
        let isChecking = false;
        let activeRequests = 0;
        const maxConcurrent = 5;
        let completed = 0;
        
        let lsKey = 'oo_link_checker_cache_v2';
        let cache = JSON.parse(localStorage.getItem(lsKey) || '{}');

        // Initial restore from cache
        rows.forEach((r, idx) => {
            let url = r.getAttribute('data-url');
            if (cache[url]) {
                let cell = r.querySelector('.oo-status-cell');
                cell.innerHTML = cache[url].html;
                // Hintergrundklassen komplett ignorieren für bessere Lesbarkeit der Links
                r.className = 'oo-link-row'; 
                r.setAttribute('data-checked', '1');
                completed++;
            }
        });

        if (completed > 0) {
            progressContainer.style.display = 'block';
            updateProgress();
        }

        btnReset.addEventListener('click', function() {
            if (confirm('Bist du sicher, dass du alle gespeicherten Ergebnisse löschen willst?')) {
                localStorage.removeItem(lsKey);
                cache = {};
                completed = 0;
                currentIndex = 0;
                rows.forEach(r => {
                    r.removeAttribute('data-checked');
                    r.className = 'oo-link-row';
                    r.querySelector('.oo-status-cell').innerHTML = '<span class="text-muted"><i class="rex-icon rex-icon-question"></i> Ausstehend</span>';
                });
                progressBar.style.width = '0%';
                progressBar.innerText = '0%';
                progressContainer.style.display = 'none';
            }
        });

        btnStart.addEventListener('click', function() {
            isChecking = true;
            btnStart.style.display = 'none';
            btnStop.style.display = 'inline-block';
            btnReset.setAttribute('disabled', 'disabled');
            progressContainer.style.display = 'block';
            
            // Advance currentIndex past already checked rows
            while(currentIndex < rows.length && rows[currentIndex].getAttribute('data-checked')) {
                currentIndex++;
            }
            
            if (currentIndex >= rows.length || completed >= rows.length) {
                // Done already
                updateProgress();
                return;
            }
            
            fillQueue();
        });

        btnStop.addEventListener('click', function() {
            isChecking = false;
            btnStart.style.display = 'inline-block';
            btnStop.style.display = 'none';
            btnReset.removeAttribute('disabled');
            btnStart.innerText = 'Fortsetzen';
        });

        function updateProgress() {
            let percent = Math.round((completed / rows.length) * 100);
            progressBar.style.width = percent + '%';
            progressBar.innerText = percent + '%';

            if (completed >= rows.length) {
                isChecking = false;
                btnStart.style.display = 'inline-block';
                btnStop.style.display = 'none';
                btnReset.removeAttribute('disabled');
                btnStart.innerText = 'Alle erledigt. Zurücksetzen für Neuscan';
                progressBar.classList.remove('active');
            }
        }

        function fillQueue() {
            if (!isChecking) return;
            
            while (activeRequests < maxConcurrent && currentIndex < rows.length) {
                let row = rows[currentIndex];
                currentIndex++;
                
                if (!row.getAttribute('data-checked')) {
                    checkRow(row);
                } else {
                    // Skip checked row
                    fillQueue(); 
                }
            }
        }

        function checkRow(row) {
            activeRequests++;
            let url = row.getAttribute('data-url');
            let cell = row.querySelector('.oo-status-cell');
            
            cell.innerHTML = '<span class="text-info"><i class="rex-icon rex-icon-refresh rex-icon-spin"></i> Prüfe...</span>';

            let fetchUrl = new URL(window.location.href);
            fetchUrl.searchParams.set('ajax_check', '1');
            fetchUrl.searchParams.set('url', url);

            fetch(fetchUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                let htmlOut = '';
                let classOut = '';
                
                if (data.status === 'ok') {
                    htmlOut = '<span class="label label-success"><i class="fa fa-check"></i> OK (' + data.code + ')</span>';
                } else if (data.status === 'warning_403') {
                    htmlOut = '<span class="label label-warning"><i class="rex-icon rex-icon-warning"></i> Bot-Schutz / 403 OK</span>';
                } else if (data.status === 'warning_405') {
                    htmlOut = '<span class="label label-warning"><i class="rex-icon rex-icon-warning"></i> Meth. geblockt (405) OK</span>';
                } else if (data.status === 'error' && data.code === 0) {
                    htmlOut = '<span class="label label-danger"><i class="rex-icon rex-icon-error"></i> Timeout / Host Not Found</span>';
                } else {
                    htmlOut = '<span class="label label-danger"><i class="rex-icon rex-icon-error"></i> Fehler (' + data.code + ')</span>';
                }
                
                cell.innerHTML = htmlOut;
                row.classList.remove('success', 'warning', 'danger');
                row.setAttribute('data-checked', '1');
                
                // Save to cache
                cache[url] = { html: htmlOut, class: '' };
                localStorage.setItem(lsKey, JSON.stringify(cache));
            })
            .catch(err => {
                let htmlOut = '<span class="label label-warning"><i class="rex-icon rex-icon-warning"></i> AJAX Fehler</span>';
                
                cell.innerHTML = htmlOut;
                row.classList.remove('success', 'warning', 'danger');
                row.setAttribute('data-checked', '1');
                
                cache[url] = { html: htmlOut, class: '' };
                localStorage.setItem(lsKey, JSON.stringify(cache));
            })
            .finally(() => {
                activeRequests--;
                completed++;
                updateProgress();
                
                if (isChecking) {
                    fillQueue();
                }
            });
        }
        
        // Single row actions: Edit & Rescan
        document.getElementById('oo-link-table').addEventListener('click', function(e) {
            // Rescan button
            if (e.target.closest('.oo-rescan-btn')) {
                let row = e.target.closest('tr');
                // Remove from cache and recheck
                let url = row.getAttribute('data-url');
                delete cache[url];
                localStorage.setItem(lsKey, JSON.stringify(cache));
                
                row.removeAttribute('data-checked');
                row.className = 'oo-link-row';
                
                if (completed > 0) completed--;
                
                if (!isChecking) {
                    checkRow(row);
                }
            }
            
            // Manual OK button
            if (e.target.closest('.oo-manual-ok-btn')) {
                let row = e.target.closest('tr');
                let url = row.getAttribute('data-url');
                let cell = row.querySelector('.oo-status-cell');
                
                let htmlOut = '<span class="label label-success"><i class="fa fa-check"></i> Manuell OK</span>';
                
                cell.innerHTML = htmlOut;
                row.classList.remove('success', 'danger', 'warning');
                row.setAttribute('data-checked', '1');
                
                // Update cache
                let wasInCache = (cache[url] !== undefined);
                cache[url] = { html: htmlOut, class: '' };
                localStorage.setItem(lsKey, JSON.stringify(cache));
                
                if (!wasInCache) {
                    completed++;
                    updateProgress();
                }
            }
            
            // Delete button
            if (e.target.closest('.oo-delete-btn')) {
                let row = e.target.closest('tr');
                let id = row.getAttribute('data-id');
                let table = row.getAttribute('data-table');
                
                if (confirm('ACHTUNG!\n\nDu bist dabei, den Datensatz mit der ID ' + id + ' (' + table + ') ENDGÜLTIG ZU LÖSCHEN.\n\nDies betrifft die Live-Website und der Eintrag wird sofort für alle Nutzer entfernt. Diese Aktion kann nicht rückgängig gemacht werden!\n\nWillst du wirklich löschen?')) {
                    let fetchUrl = new URL(window.location.href);
                    fetchUrl.searchParams.set('ajax_delete', '1');
                    fetchUrl.searchParams.set('id', id);
                    if (table) {
                        fetchUrl.searchParams.set('table', table);
                    }
                    
                    fetch(fetchUrl.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            // Find all rows with this ID and Table
                            let matchingRows = document.querySelectorAll('.oo-link-row[data-id="' + id + '"][data-table="' + table + '"]');
                            matchingRows.forEach(r => {
                                if (r.getAttribute('data-checked') && completed > 0) completed--;
                                let url = r.getAttribute('data-url');
                                delete cache[url];
                                r.remove(); // Removes the element from the DOM
                            });
                            localStorage.setItem(lsKey, JSON.stringify(cache));
                            updateProgress();
                            alert('Der Datensatz wurde erfolgreich gelöscht und aus der Liste entfernt.');
                        } else {
                            alert('Fehler beim Löschen des Datensatzes.');
                        }
                    });
                }
            }
        });
    });
</script>