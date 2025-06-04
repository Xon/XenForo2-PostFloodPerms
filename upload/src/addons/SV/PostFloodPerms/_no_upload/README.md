#  Flood Permissions

Provides user group permissions for the following post rate limiting options:

Recommend [Redis flood check](https://xenforo.com/community/resources/redis-flood-check.5564/) for when permission-based flood limits are longer than the ~30 seconds.

Thread/Post Permissions;
- React - Enable per thread rate limiting
- React - Per thread rate limiting - delay between reacts in seconds
- React - Enable per node rate limiting
- React - Per node post rate limiting - delay between reacts in seconds
- React - General rate limiting - delay between posts in seconds

- Post Reply - Enable per thread rate limiting
- Post Reply - Per thread rate limiting - delay between posts in seconds
- Post Reply - Enable per node rate limiting
- Post Reply - Per node post rate limiting - delay between posts in seconds
- Post Reply - General rate limiting - delay between posts in seconds

- Delete - Enable per thread rate limiting
- Delete - Per thread rate limiting - delay between deleting posts in seconds
- Delete - Enable per node rate limiting
- Delete - Per node post rate limiting - delay between deleting posts in seconds
- Delete - General rate limiting - delay between posts in seconds

- New Thread - Enable per node rate limiting
- New Thread - Per node post rate limiting - delay between new threads in seconds
- New Thread - General rate limiting - delay between new threads in seconds

Conversation Permissions;
- React - Enable per conversation rate limiting
- React - Per conversation rate limiting - delay between reacts in seconds
- React - General rate limiting - delay between reacts in seconds

- Post Reply - Enable per conversation rate limiting
- Post Reply - Per conversation rate limiting - delay between conversation messages in seconds
- Post Reply - General rate limiting - delay between conversation messages (including new conversations!) in seconds

Profile Post permissions:
- React - Per profile post rate limiting - delay between reacts in seconds
- React - Enable per profile post rate limiting
- React - General rate limiting - delay between reacts in seconds

- New Profile Post - Enable per profile rate limiting
- New Profile Post - Per profile rate limiting - delay between new profile post per user profile in seconds
- New Profile Post - General rate limiting - delay between new profile post in seconds

- Post comment - Enable per profile post limiting
- Post comment - Per profile post rate limiting - delay between profile post comments in seconds
- Post comment - General rate limiting - delay between profile post comment in seconds

NixFifty's Ticket Permissions;
- Post Reply - Enable per ticket rate limiting
- Post Reply - Per ticket rate limiting - delay between ticket messages in seconds
- Post Reply - Enable per ticket category rate limiting
- Post Reply - Per ticket category rate limiting - delay between ticket messages in seconds
- Post Reply - General rate limiting - delay between ticket messages (including new tickets!) in seconds

This permits the posting/react rate to be managed per node, and per user group. The number is the delay in seconds between posts.

Minimum supported delay is 1 second. A value of zero disables that rate limiter (ie falls through to the next check), and a value of unlimited causes it to be the equivalent of zero seconds delay

This is due to how XenForo permissions inherited with numeric.

The per thread/node option allows decoupling of the global flood limiter from posting in different sections.

Matching order, **the first match wins**:
- Per thread rate limiting.
- Per node rate limiting.
- General post rate limiting.

The XF global flood check (`Minimum time between messages`) is skipped if any of the above matches are configured.

No extra queries required.