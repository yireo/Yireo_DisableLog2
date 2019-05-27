# Backgrounds
The Magento 2 core has an excellent logging system: Every time that a customer logs in or out, his/her details are logged. This might lead to a big database size over time. Not everybody wants this: There are
numerous threads discussing the periodic cleaning of those tables using the SQL statement `TRUNCATE`. But this is only part of the problem.

Besides filling up the database, this logging also causes various extra SQL `INSERT` statements to be made for every pageload. If you have a busy site, this certainly has an impact on database-performance.

Now, if you're relying on those Magento 2 log statistics, you'll just have to accept this as a fact. However, if you're using other external services like Piwik or GoogleAnalytics, this Magento logging is actually
an unwanted feature.

## Our extension
Our extension gives you the power to disable all this Magento 2 logging: Either for all requests being made, or only for search-robots. It disables logging, but also disables the autoincrement flag for certain
search patterns: The latter is interesting if you are interested in what your real-life customers are actually searching for, while by default search engines mess up these statistics.

## Technical specifications
This extension makes use of interceptors (aka plugins) to override various Magento 2 core classes. See the `etc/di.xml` file for details.
