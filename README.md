#  PostFloodPerms

Provides user group permissions for the following post rate limiting options:

- Post Reply - Enable Per thread rate limiting
- Post Reply - Per thread rate limiting - delay between posts in seconds
- Post Reply - Enable Per node rate limiting
- Post Reply - Per node post rate limiting - delay between posts in seconds
- Post Reply - General rate limiting - delay between posts in seconds
- Like/React - Enable Per thread rate limiting
- Like/React - Per thread rate limiting - delay between posts in seconds
- Like/React - Enable Per node rate limiting
- Like/React - Per node post rate limiting - delay between posts in seconds
- Like/React - General rate limiting - delay between posts in seconds
- Delete - Enable Per thread rate limiting
- Delete - Per thread rate limiting - delay between deleting posts in seconds
- Delete - Enable Per node rate limiting
- Delete - Per node post rate limiting - delay between deleting posts in seconds
- Delete - General rate limiting - delay between posts in seconds



This permits the posting/Liking rate to be managed per node, and per user group. The number is the delay in seconds between posts.

Minimum supported delay is 1 second. A value of zero disables that rate limiter (ie falls through to the next check), and a value of unlimited causes it to be the equivalent of zero seconds delay

This is due to how XenForo permissions inherited with numeric.

The per thread/node option allows decoupling of the global flood limiter from posting in different sections.

Matching order:
- Per thread rate limiting.
- Per node rate limiting.
- General post rate limiting.
- XF Global post rate limiting (reports/posts/profile posts/etc).

No extra queries required.