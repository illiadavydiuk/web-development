// 1
let a = 10;
let b = 5;

console.log("--- Завдання 1 ---");
console.log("Сума:", a + b);
console.log("Різниця:", a - b);
console.log("Множення:", a * b);
console.log("Ділення:", a / b);

// 2
const firstName = "Ілля";
const lastName = "Давидюк";
const fullName = `${firstName} ${lastName}`;

console.log("\n--- Завдання 2 ---");
console.log(`Привіт, ${fullName}!`);

// 3
let age = 19;

console.log("\n--- Завдання 3 ---");
if (age >= 18) {
    console.log("Доступ дозволено");
} else {
    console.log("Доступ заборонено");
}

// 4
console.log("\n--- Завдання 4 ---");
for (let i = 1; i <= 10; i++) {
    console.log(i);
}

// 5
function square(number) {
    return number * number;
}

console.log("\n--- Завдання 5 ---");
let result = square(5);
console.log("Квадрат числа 5 дорівнює:", result);

// 6
let fruits = ["Яблуко", "Банан", "Апельсин"];
fruits.push("Манго");

console.log("\n--- Завдання 6 ---");
console.log("Список фруктів:", fruits);