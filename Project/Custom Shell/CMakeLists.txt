cmake_minimum_required(VERSION 3.10)
project(NDStest)

set(CMAKE_CXX_STANDARD 14)

add_executable(
    NDStest

    src/main.cpp src/header.hpp
    src/shell/Shell.cpp src/shell/Shell.h
    src/ldap/LDAPClient.cpp src/ldap/LDAPClient.h
    src/shell/ShellCommand.h
)

target_link_libraries(NDStest ldap)
target_link_libraries(NDStest ldapcpp)
