# Makefile for NDS Custom Shell ###  ###  ###

CC=g++
BIN=custshell
SRC=main.cpp Shell.cpp LDAPClient.cpp
FLAGS=-std=c++14 -Wall -Wextra -pedantic

# ###  ###  ###  ###  ###  ###  ###  ###  ###

all:
	$(CC) -o $(BIN) $(SRC) $(FLAGS)

debug:
	$(CC) -o $(BIN) $(SRC) $(FLAGS) -DDEBUG

clean:
	rm -rf $(BIN)

# ###  ###  ###  ###  ###  ###  ###  ###  ###

