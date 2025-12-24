1. Update `EditScheduleModal.vue`.
   - Add `is_online` (boolean) to `form` state.
   - Initialize `is_online` from `schedule.is_online` or `schedule.room_id` context.
     - Note: DB likely has `is_online` column?
     - Or `room_id` is null when online?
     - Let's check `schedules` table schema.
   - Add Toggle/Select/Radio for "Mode Perkuliahan" (Offline / Online).
   - Logic: If Online, `room_id` might be disabled or set to null? Or specific "Online Room"? 
     - User request: "merubah mode kuliah dari offline ke online".
   
2. Room Logic
   - User said: "untuk ruangan tidak perlu di filter".
   - My `SearchableSelect` shows all rooms (filtered by search query).
   - Maybe user meant: "Don't filter the list when typing"? (Unlikely).
   - Maybe user meant: "Don't apply the 'show all if matches selected' logic"?
   - Most likely: User just re-affirming "Room list should be complete" unlike Lecturer list which is limited to Team. I will ensure Room list is FULL.
   
3. Verify `is_online` column exists.
   - Check migration or model.
