cmake_minimum_required(VERSION 3.10)
project(NDStest C)

set(CMAKE_C_STANDARD 99)

add_executable(NDStest main.c)
target_link_libraries(NDStest ldap)
