<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <h1>Manage All Doctor Schedules</h1>
    <p><a href="/doctor/schedules" class="btn">← Back to My Schedules</a></p>
    
    <?php if ($error): ?>
        <div style="background: #fee; color: #c33; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div style="background: #dfd; color: #363; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <!-- Today's Schedules -->
    <div style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>📅 Today's Schedules (<?= date('l, F j, Y') ?>)</h2>
        
        <?php if (empty($today_schedules)): ?>
            <p>No schedules for today.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($today_schedules as $schedule): ?>
                        <tr>
                            <td><?= htmlspecialchars($schedule['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($schedule['spec_name'] ?? 'N/A') ?></td>
                            <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                            <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                    background: <?= $schedule['is_available'] ? '#d4edda' : '#f8d7da' ?>; 
                                    color: <?= $schedule['is_available'] ? '#155724' : '#721c24' ?>;">
                                    <?= $schedule['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Add New Schedule -->
    <div style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>Add New Schedule</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Doctor: <span style="color: red;">*</span></label>
                    <select name="doc_id" required>
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['doctor_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Schedule Date: <span style="color: red;">*</span></label>
                    <input type="date" name="schedule_date" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Start Time: <span style="color: red;">*</span></label>
                    <input type="time" name="start_time" required>
                </div>
                
                <div class="form-group">
                    <label>End Time: <span style="color: red;">*</span></label>
                    <input type="time" name="end_time" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Max Appointments:</label>
                    <input type="number" name="max_appointments" value="10" min="1" max="50">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_available" value="1">
                        Available for appointments
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">Add Schedule</button>
        </form>
    </div>
    
    <!-- All Schedules -->
    <div style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>All Schedules</h2>
        
        <?php if (empty($all_schedules)): ?>
            <p>No schedules found.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Max Appts</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_schedules as $schedule): ?>
                        <tr>
                            <td><?= $schedule['schedule_id'] ?></td>
                            <td><?= htmlspecialchars($schedule['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($schedule['spec_name'] ?? 'N/A') ?></td>
                            <td><?= date('M j, Y', strtotime($schedule['schedule_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                            <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                            <td><?= $schedule['max_appointments'] ?? 10 ?></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                    background: <?= $schedule['is_available'] ? '#d4edda' : '#f8d7da' ?>; 
                                    color: <?= $schedule['is_available'] ? '#155724' : '#721c24' ?>;">
                                    <?= $schedule['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <button onclick='editSchedule(<?= json_encode($schedule) ?>)' class="btn btn-primary btn-sm">Edit</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this schedule?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $schedule['schedule_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="background: white; margin: 100px auto; padding: 30px; width: 90%; max-width: 600px; border-radius: 8px;">
        <h2>Edit Schedule</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-group">
                <label>Doctor:</label>
                <select name="doc_id" id="edit_doc_id" required>
                    <option value="">Select Doctor</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['doctor_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Schedule Date:</label>
                <input type="date" name="schedule_date" id="edit_schedule_date" required>
            </div>
            
            <div class="form-group">
                <label>Start Time:</label>
                <input type="time" name="start_time" id="edit_start_time" required>
            </div>
            
            <div class="form-group">
                <label>End Time:</label>
                <input type="time" name="end_time" id="edit_end_time" required>
            </div>
            
            <div class="form-group">
                <label>Max Appointments:</label>
                <input type="number" name="max_appointments" id="edit_max_appointments" min="1" max="50" required>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="is_available" id="edit_is_available" value="1" style="margin-right: 10px; width: auto;">
                    <span>Available for appointments</span>
                </label>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-success">Update Schedule</button>
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editSchedule(schedule) {
    document.getElementById('edit_id').value = schedule.schedule_id;
    document.getElementById('edit_doc_id').value = schedule.doc_id;
    document.getElementById('edit_schedule_date').value = schedule.schedule_date;
    document.getElementById('edit_start_time').value = schedule.start_time;
    document.getElementById('edit_end_time').value = schedule.end_time;
    document.getElementById('edit_max_appointments').value = schedule.max_appointments || 10;
    document.getElementById('edit_is_available').checked = schedule.is_available == 1;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
