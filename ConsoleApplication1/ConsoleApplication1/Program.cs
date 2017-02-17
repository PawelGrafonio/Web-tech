using System;

class Program
{

    static string Caesar(string value, int x,int y)
    {

        char[] buffer = value.ToCharArray();
        for (int i = 0; i < buffer.Length; i++)
        {

            // Letter.
            char letter = buffer[i];
            int shift = (letter * x+y) % 26;
            letter = (char)('a' +(shift));
            if (letter > 'z')
            {
                letter = (char)(letter - 26);
            }
            else if (letter < 'a')
            {
                letter = (char)(letter + 26);
            }
            // Store.
            buffer[i] = letter;
        }
        return new string(buffer);
    }


    static string Caesar_D(string value, int x, int y)
    {
        char[] buffer = value.ToCharArray();
        for (int i = 0; i<buffer.Length; i++)
        {

            // Letter.
            char letter = buffer[i];
            int shift = ((1/x*(letter - y))%26);
            letter = (char)('a' + (shift));
            // Subtract 26 on overflow.
            // Add 26 on underflow.
            if (letter > 'z')
            {
                letter = (char)(letter - 26);
            }
            else if (letter< 'a')
            {
                letter = (char)(letter + 26);
            }
            // Store.
            buffer[i] = letter;
        }
        return new string(buffer);
    }



    static void Main()
    {
        string a = "kuma";
        string b = Caesar(a, 3, 5); // Ok
        string c = Caesar_D(b, 3, 5); // Ok
        string z = "hope";
         string d = Caesar(z, 3,5); // Ok
         string e = Caesar_D(d, 5,3); // Ok

        /*string f = "exxegoexsrgi";
        string g = Caesar(f, -4); // Ok

        string h = "poi";
        string i =Caesar_s(h, 2);
        string j = Caesar(i, -2);*/

        Console.WriteLine(a);
        Console.WriteLine(b);
        Console.WriteLine(c);
        Console.WriteLine(z);
        Console.WriteLine(d);
        Console.WriteLine(e);
        /*Console.WriteLine(f);
       Console.WriteLine(g);
       Console.WriteLine(h);
       Console.WriteLine(i);
       Console.WriteLine(j);*/
    }
}