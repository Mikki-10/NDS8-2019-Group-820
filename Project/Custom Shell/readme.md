# Custom shell for Telenor project

* Made by Andrej, Pierre and Sebastian

#### Shell Functionality

* Displays user the available critical systems (LDAP client module) and handles the choice (Shell module)
* Logs all activity the remote supporter does (Logging module)



#### LDAP

* phpLDAPadmin: https://192.168.20.4:8443
  * <https://github.com/osixia/docker-openldap>
  * <https://hub.docker.com/search?q=openldap&type=image>

* <https://stackoverflow.com/questions/45511696/creating-a-new-objectclass-and-attribute-in-openldap>
* https://guillaumemaka.com/2013/07/17/openldap-create-a-custom-ldap-schema.html



* Some good-looking query
  ![](readme_img/query_001.png)

* Result from this query:

  ![](readme_img/query_output.png)

* Result from the custom shell

![](readme_img/shell_output.png)



#### Some links

* [Using CLion for remote development](<https://www.youtube.com/watch?v=g1zPcja3zAU>)
  * [Manual](<https://blog.jetbrains.com/clion/2018/09/initial-remote-dev-support-clion/>), [Another manual](<https://www.jetbrains.com/help/clion/remote-projects-support.html>)
  * [Cmake: -lldap](<https://stackoverflow.com/questions/34625627/how-to-link-to-the-c-math-library-with-cmake>)
* LDAPc++
  * [example](<https://github.com/openldap/openldap/blob/master/contrib/ldapc%2B%2B/examples/main.cpp>), [repository](<https://github.com/openldap/openldap/tree/master/contrib/ldapc%2B%2B>), [presentation](<https://www.openldap.org/conf/odd-tuebingen-2006/Ralf.pdf>)

  * installed on LDAP server (192.168.20.4), using git clone and following the installation

    * sudo apt install libldap-dev
    * sudo apt install libsasl2-dev
    * ./configure
    * sudo make install
    * sudo ldconfig

* LDAP documentation

  * [Index](<https://linux.die.net/man/3/ldap>), [Extended Synchronous LDAP Search](<https://linux.die.net/man/3/ldap_search_ext_s>) - shouldn't be required anymore

* Logging, JSON
  * <https://github.com/nlohmann/json>