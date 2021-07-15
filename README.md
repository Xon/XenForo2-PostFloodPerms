#  Flood Permissions

Provides user group permissions for the following post rate limiting options:

Thread/Post Permissions;
- New Thread - Enable Per node rate limiting
- New Thread - Per node post rate limiting - delay between new threads in seconds
- New Thread - General rate limiting - delay between new threads in seconds

- Post Reply - Enable Per thread rate limiting
- Post Reply - Per thread rate limiting - delay between posts in seconds
- Post Reply - Enable Per node rate limiting
- Post Reply - Per node post rate limiting - delay between posts in seconds
- Post Reply - General rate limiting - delay between posts in seconds

- React - Enable Per thread rate limiting
- React - Per thread rate limiting - delay between reacts in seconds
- React - Enable Per node rate limiting
- React - Per node post rate limiting - delay between reacts in seconds
- React - General rate limiting - delay between posts in seconds

- Delete - Enable Per thread rate limiting
- Delete - Per thread rate limiting - delay between deleting posts in seconds
- Delete - Enable Per node rate limiting
- Delete - Per node post rate limiting - delay between deleting posts in seconds
- Delete - General rate limiting - delay between posts in seconds

Conversation Permissions;
- Post Reply - Enable Per conversation rate limiting
- Post Reply - Per conversation rate limiting - delay between conversation messages in seconds
- Post Reply - General rate limiting - delay between conversation messages (including new conversations!) in seconds

- React - Per conversation rate limiting - delay between reacts in seconds
- React - Enable Per conversation rate limiting
- React - General rate limiting - delay between reacts in seconds


This permits the posting/react rate to be managed per node, and per user group. The number is the delay in seconds between posts.

Minimum supported delay is 1 second. A value of zero disables that rate limiter (ie falls through to the next check), and a value of unlimited causes it to be the equivalent of zero seconds delay

This is due to how XenForo permissions inherited with numeric.

The per thread/node option allows decoupling of the global flood limiter from posting in different sections.

Matching order, **the first match wins**:
- Per thread rate limiting.
- Per node rate limiting.
- General post rate limiting.

The XF global flood check ("Minimum time between messages") is skipped if any of the above matches are configured.

No extra queries required.