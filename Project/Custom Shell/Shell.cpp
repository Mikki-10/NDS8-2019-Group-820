//
// Custom Shell [ Pierre & Andrej ]
//

#include "Shell.h"

Shell::Shell() {

    String delimiter(" \t\r\n\a");
    for (bool & t : token_delimiter) t = false;
    for (auto & c : delimiter) token_delimiter[static_cast<int>(c)] = true;

    // ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  --- Commands

    addCommand(ShellCommand("help",
        [&] (StringVec p) {
            unused(p);

            out("Test Shell");
            out("Please type the number corresponding to the system you want to access, and hit enter.");
            out("The following commands are built in:");
            for (auto pair : this->commands) {
                out("  " + pair.second.getName());
            }
            out("Use the man command for information on other programs.");
            return true;
        }
    ));

    addCommand(ShellCommand("exit",
        [](StringVec p) {
            unused(p);
            return false;
        }
    ));

    addCommand(ShellCommand("lsh_1",
        [] (StringVec p) {
            unused(p);
            out("You are going to be redirected to System A");
            //FILE *f = popen( "ssh -t -t nds@192.168.40.1 -p 50222", "r" );
            return true;
        }
    ));

    addCommand(ShellCommand("lsh_2",
        [] (StringVec p) {
            unused(p);
            // out("You are going to be redirected to System B");
            return true;
        }
    ));

}

Shell::~Shell() = default;

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

void Shell::addCommand(ShellCommand cmd) {
    commands.insert(std::make_pair(cmd.getName(), cmd));
}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

Retval Shell::call(const String& commandName) {

    auto cmd = commands.find(commandName);
    if (cmd != commands.end()) {
        return cmd->second() ? Retval::SUCCESS : Retval::FAILURE;
    }
    else {
        return Retval::NOT_FOUND;
    }

}


Retval Shell::call(const String &commandName, StringVec parameters) {

    auto cmd = commands.find(commandName);
    if (cmd != commands.end()) {
        return cmd->second(parameters) ? Retval::SUCCESS : Retval::FAILURE;
    }
    else {
        return Retval::NOT_FOUND;
    }

}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

StringVec Shell::getInputLine() {

    String line;
    StringVec res;
    String token;

    std::getline(std::cin, line);

    for (auto &c : line) {

        // iterate over each char and check for delimiter
        if (c >= 0 && token_delimiter[static_cast<int>(c)]) {
            if (!token.empty()) {
                res.push_back(token);
                token.clear();
            }
        }
        else {
            token += c;
        }
    }

    if (!token.empty()) {
        res.push_back(token);
    }

    return res;

}

void Shell::loop() {

    int status;
    do {

        std::cout << ":> ";

        StringVec line = getInputLine();
        if (line.empty()) continue;

        String cmd = line.at(0);
        line.erase(line.begin());

        status = execute(cmd, line);
//        out(cmd + " returned " + str(status));
//        std::cout << "<<" << cmd << ">> ";
//        for (const auto& item : line) {
//            std::cout << "[" << item << "] ";
//        }
//        out("");

    } while (status);

}

int Shell::execute(const String& commandName, const StringVec& parameters) {

    Retval r = call(commandName, parameters);
    if (r == Retval::NOT_FOUND) {
        // call external bash shell, let's go low
        String assembled = String(commandName) + " ";
        for (auto& p : parameters) {
            assembled += String(p) + " ";
        }

        // https://www.geeksforgeeks.org/system-call-in-c/
        // TODO: is int system(char*) viable?
        system(assembled.c_str());
        return Retval::SUCCESS;

    }
    return r;

}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---
