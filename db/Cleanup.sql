/*
 * These scripts help cleanup the database
 */

-- Identify user account "series" (e.g., Hahiho1, Hahiho2, Hahiho3, etc.)
select * from user where display_name like 'Hahiho%';

-- Remove an account "series"
delete from user where display_name like 'Hahiho%';

-- Attempt to identify other user account "series"
select substr(display_name from 1 for 7), count(*) as total
  from user
 group by 1
having count(*) > 3
 order by 2
;

-- Remove "fake user accounts" (i.e., those created by a script)
delete from user where email not like '%@%.%' and length(email) > 80;

-- Remove orphaned progress records after deleting fake accounts
delete from progress where user_id not in (select id from user);

